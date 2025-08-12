<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Employee;
use App\Models\Calendar;
use App\Models\Room;
use App\Models\Record;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PDF;
use Illuminate\Support\Collection;
use App\Http\Traits\RecordTrait;
use App\Models\IssueListing;
use App\Models\SupplyListing;

class PdfController extends Controller
{
   use RecordTrait;
    /**
     * Generate a pdf for calendar (1 month for user on device).
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar_horizontal(Device $device, Employee $employee, Request $request)
    {
      $date = request('date') ? request('date') : Carbon::now();
      try {
          $date = Carbon::parse($date);
      } catch (\Exception $e) {
          return redirect()->route('calendars.index')->with([ 'error' => __('Date is not valid'), 'message' => ['exception' => $e->getMessage()] ]);
      }

      $from = $date->firstOfMonth();
      $to = $date->copy()->lastOfMonth();

      $period = CarbonPeriod::create($from, $to);

      $calendars = Calendar::with('rooms')
      ->where('device_id', $device->id)
      ->where('employee_id', $employee->id)
      ->whereBetween('date', [$from, $to])
      ->get();

      $records = Record::with('calendar.rooms')
      ->where('device_id', $device->id)
      ->where('employee_id', $employee->id)
      ->whereBetween('time', [$from, $to])
      ->where('action', 1)
      ->get();      


      $employee_records = Record::where('employee_id', $employee->id)
      ->where(function ($q) use ($from, $to){
        $q->whereBetween( 'time', [$from, $to] );
      })
      ->select('id', 'device_id', 'action', 'time', 'perform', 'identity')
      ->with('device')
      ->with('calendar.rooms:id,name,category')
      ->orderBy('time', 'ASC')
      ->get();
      $period = CarbonPeriod::create($from, '1 day', $to);

      $matrix = $this->month_employee_matrix($period, $employee_records);
      
      $footer = new Collection();
      
      foreach ($matrix as $matrix_key => $day) {
        $footer->put( $matrix_key, collect() );
        $footer[$matrix_key] = collect();
        $pausein_time = $pauseout_time = 0;
        foreach ($day as $key => $records) {
          $footer[$matrix_key] ->put( $key, collect() );
          $pausein_time = $pauseout_time = 0;

          $filtered = $records->where('action', 0);
          if ( $filtered->count() > 0 ) {
            $footer[$matrix_key][$key]->put('from', $records->firstWhere('action', 0)->time->format('H:i'));
          } else {
            $footer[$matrix_key][$key]->put('from', '---');
          }

          $filtered = $records->where('action', 1);
          if ( $filtered->count() > 0 ) {
            $footer[$matrix_key][$key]->put('to', $records->firstWhere('action', 1)->time->format('H:i'));
          } else {
            $footer[$matrix_key][$key]->put('to', '---');
          }
          
          // Pause in
          $filtered = $records->where('action', 2);
          $pausein_arr = $filtered->pluck('time');
          if ( $filtered->count() > 0 ) {
            if ( $filtered->count() > 1 ) {
              foreach ($filtered as $pin_record) {
                $pausein_time += strtotime( $pin_record->time );
              }
            } else {
              $pausein_time = strtotime( $filtered->first()->time );
            }
          }
          
          // Pause out
          $filtered = $records->where('action', 3);
          $pauseout_arr = $filtered->pluck('time');
          if ( $filtered->count() > 0 ) {
            if ( $filtered->count() > 1 ) {
              foreach ($filtered as $pout_record) {
                $pauseout_time += strtotime( $pout_record->time );
              }
            } else {
              $pauseout_time = strtotime( $filtered->first()->time );
            }
          }
          
          if ( $pausein_time && $pauseout_time ) {
            $t = $pauseout_time - $pausein_time;
            $footer[$matrix_key][$key]->put('pause', sprintf('%02d:%02d', ($t/3600),($t/60%60)));
          } else {
            $footer[$matrix_key][$key]->put('pause', '00:00');
          }
          

        }
      }

      // return view('pages.pdfs.calendar_horizontal', compact('device', 'employee', 'period', 'calendars', 'matrix', 'footer')); 

      $pdf = PDF::loadView('pages.pdfs.calendar_horizontal', compact('device', 'employee', 'period', 'calendars', 'matrix', 'footer'))
      ->setOptions(['defaultFont' => 'sans-serif'])
      ->setPaper('a4', 'portrait');

      return $pdf->stream();
      // return $pdf->download('calendar.pdf');
    }

    /**
     * Generate a pdf for calendar (1 day for all users on device).
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar_vertical(Device $device, Request $request)
    {

      $date = request('date') ? request('date') : Carbon::now();
      try {
          $date = Carbon::parse($date);
      } catch (\Exception $e) {
          return redirect()->route('calendars.index')->with([ 'error' => __('Date is not valid'), 'message' => ['exception' => $e->getMessage()] ]);
      }

      $from = $date->startOfDay();  //today - start of day
      // $middle = $from->copy()->endOfDay(); //today - end of day
      $to = $date->copy()->addDay()->endOfDay(); //add day for night shift, just in case he has

      $employees = $device->employees()
      ->with(['calendars' => function ($q) use($device, $from, $to) {
        $q->whereBetween('calendars.date', [$from, $to])
          ->where('calendars.device_id', $device->id);
        $q->with('rooms');
      }])
        // the following block prevents cases where ex. day is 21.05.2022
        //and there are no records on 21st but there are on 22nd
        // THIS IS BUGGY ON PDF Calendar AS IT SKIPS RECORDS NEEDED
      // ->whereHas('records', function ($q) use($from, $middle) {
      //   $q->whereBetween('time', [$from, $middle]);
      // })
      ->has('calendars')
      ->whereHas('calendars', function ($q) use($date) {
          $q->where('date', $date);
      })
      ->with(['records' => function ($record) use($from, $to) {
        return $record->whereBetween('time', [$from, $to])->orderBy('time', 'ASC');
      }])
      ->orderBy('function')
      ->orderBy('name')
      ->get();

      // return $employees->find(16)->records->find(2366)->rooms[0]->pivot->status;

      $employees_func = collect();
      $employees_func->push($employees);
      

      // remove calendars added for next day
      foreach ($employees as $key => $employee) {
        foreach ($employee->calendars as $key => $calendar) {
          if ( !$date->isSameDay( $calendar->date ) ) {
            $employee->calendars->forget($key);
            continue;
          }
        }
      }

      $daily_employees = $this->daily_employees_matrix( $employees_func, $from );

      $footer = new Collection();
      
      foreach ($daily_employees as $employee) {
        $footer->put( $employee->id, collect() );
        $footer[$employee->id] = collect();
        $pausein_time = $pauseout_time = 0;
        if ( isset( $employee->matrix ) ) {
          foreach ($employee->matrix as $key => $matrixes) {
            $today = null;
            $footer[$employee->id] ->put( $key, collect() );
            $pausein_time = $pauseout_time = 0;

            $filtered = $matrixes->where('action', 0);
            if ( $filtered->count() > 0 ) {
              $today = $filtered->first()->time->format('d');
              $footer[$employee->id][$key]->put('from', $filtered->first()->time->format('H:i'));
            } else {
              $footer[$employee->id][$key]->put('from', '---');
            }

            $filtered = $matrixes->where('action', 1);
            if ( $filtered->count() > 0 ) {
              $night = ($today!=null && $today != $filtered->first()->time->format('d')) ? ' (n)' : '';
              $footer[$employee->id][$key]->put('to', $filtered->first()->time->format('H:i') . $night );
            } else {
              $footer[$employee->id][$key]->put('to', '---');
            }

            // Pause in
            $filtered = $matrixes->where('action', 2);
            $pausein_arr = $filtered->pluck('time');
            if ( $filtered->count() > 0 ) {
              if ( $filtered->count() > 1 ) {
                foreach ($filtered as $pin_record) {
                  $pausein_time += strtotime( $pin_record->time );
                }
              } else {
                $pausein_time = strtotime( $filtered->first()->time );
              }
            }
            
            // Pause out
            $filtered = $matrixes->where('action', 3);
            $pauseout_arr = $filtered->pluck('time');
            if ( $filtered->count() > 0 ) {
              if ( $filtered->count() > 1 ) {
                foreach ($filtered as $pout_record) {
                  $pauseout_time += strtotime( $pout_record->time );
                }
              } else {
                $pauseout_time = strtotime( $filtered->first()->time );
              }
            }

            if ( $pausein_time && $pauseout_time ) {
              $t = $pauseout_time - $pausein_time;
              $footer[$employee->id][$key]->put('pause', sprintf('%02d:%02d', ($t/3600),($t/60%60)));
            } else {
              $footer[$employee->id][$key]->put('pause', 0);
            }
            
          }
        }
      }

      // return view('pages.pdfs.calendar_vertical', compact('device', 'employees', 'date', 'footer'));

      $pdf = PDF::loadView('pages.pdfs.calendar_vertical', compact('device', 'employees', 'date', 'footer'))
      ->setOptions(['defaultFont' => 'sans-serif'])
      ->setPaper('a4', 'portrait');

      return $pdf->stream();
      // return $pdf->download('calendar.pdf');

    }

    /**
     * Generate a pdf for calendar (1 month for all users on device).
     *
     * @return \Illuminate\Http\Response
     */
    public function calendar_month(Device $device, Request $request)
    {
      $date = request('date') ? request('date') : Carbon::now();
      try {
          $date = Carbon::parse($date);
      } catch (\Exception $e) {
          return redirect()->route('calendars.index')->with([ 'error' => __('Date is not valid'), 'message' => ['exception' => $e->getMessage()] ]);
      }

      $page_title = __('Calendar for ') . $date->format('F Y');;
      $page_description = __('Device: ') . $device->name;

      // $cDate = new Carbon($date);
      $from = $date->firstOfMonth();
      $to = $date->copy()->lastOfMonth();

      $rooms = Room::where('device_id', $device->id)->get();
      $rooms = $rooms->groupBy('category');

      $period = CarbonPeriod::create($from, $to);
      $data = $device->employees()
      ->where('start', '<=', $to)
      ->where('end', '>=', $from)
      ->with(['calendars' => function ($q) use($from, $to) {
        $q->whereBetween('date', [$from, $to]);
      }])
      ->orderBy('function')
      ->orderBy('name')
      ->get();

      // return view('pages.pdfs.calendar_horizontal', compact('device', 'employee', 'period', 'calendars'));
      $pdf = PDF::loadView('pages.pdfs.calendar_month', compact('data', 'period', 'device', 'date', 'rooms'))
      ->setOptions(['defaultFont' => 'sans-serif'])
      ->setPaper('a4', 'landscape');

      return $pdf->stream();
      // return $pdf->download('calendar.pdf');

    }

    /**
     * Generate a pdf for issues.
     *
     * @return \Illuminate\Http\Response
     */
    public function issues(Device $device, Request $request)
    {
      // $this->authorize('viewAny', Issues::class);

      $assigned_devices = [];
      if ( auth()->user()->hasRole(['super_admin', 'admin']) ) {
        $assigned_devices = Device::all()->pluck('id'); // get all devices
      } else {
        $assigned_devices = auth()->user()->devices->pluck('id'); //get just assigned devices
      }
      
      
      // defaults
      $fixed = $request->get('type') == 1 ? 1 : 0;
      $start = $request->start != 'null' ? Carbon::parse(  $request->start )->startOfDay() : null;
      $end = $request->end != 'null' ? Carbon::parse(  $request->end )->endOfDay() : null;
      $search = $request->has('search') ? $request->get('search') : null;
      $device_id = $request->has('device') ? $request->get('device') : null;


      $listings = IssueListing::with('userRequested')
      ->with('issue:id,name')
      ->with('room:id,name')
      ->withWhereHas('room', fn($query) =>
        $query->whereIn('rooms.device_id', $assigned_devices)
      )
      ->with('room.device')
      ->with('media')
      ->where('done', $fixed)
      ->where(function ($q) use ( $search, $start, $end, $device_id ){

        if( $search ){
          $q->whereHas( 'issue', function($k) use ($search) {
              $k->where( 'name', 'like', "%{$search}%" );
          })
          ->orWhereHas( 'room', function($k) use ($search) {
            $k->where( 'name', 'like', "%{$search}%" );
          })
          ->orWhereHas( 'userRequested', function($k) use ($search) {
            $k->where( 'name', 'like', "%{$search}%" );
          });
        }

        if ( $start && $end ) {
          $q->whereBetween( 'date_requested', [$start, $end] );
        }

        if( $device_id ){
          $q->whereHas('room', function($q) use ($device_id){
            $q->where('device_id', $device_id);
          });
        }

      })
      ->get();

      $devices = $listings->pluck('room.device.name')->unique()->implode(', ');
      $period = $start . ' - ' . $end;
      $query = $search;

      $pdf = PDF::loadView('pages.pdfs.issues', compact('listings', 'devices', 'period', 'query'))
      ->setOptions(['defaultFont' => 'sans-serif'])
      ->setPaper('a4', 'landscape');
      // return view('pages.pdfs.issues', compact('listings', 'devices', 'period', 'query'));
      // return $pdf->stream('issues.pdf');
      return $pdf->download('issues.pdf');

    }

    /**
     * Generate a pdf for supplies.
     *
     * @return \Illuminate\Http\Response
     */
    public function supplies(Device $device, Request $request)
    {
      // $this->authorize('viewAny', Supplies::class);

      $assigned_devices = [];
      if ( auth()->user()->hasRole(['super_admin', 'admin']) ) {
        $assigned_devices = Device::all()->pluck('id'); // get all devices
      } else {
        $assigned_devices = auth()->user()->devices->pluck('id'); //get just assigned devices
      }
      
      
      // defaults
      $fixed = $request->get('type') == 1 ? 1 : 0;
      $start = $request->start != 'null' ? Carbon::parse(  $request->start )->startOfDay() : null;
      $end = $request->end != 'null' ? Carbon::parse(  $request->end )->endOfDay() : null;
      $search = $request->has('search') ? $request->get('search') : null;
      $device_id = $request->has('device') ? $request->get('device') : null;


      $listings = SupplyListing::with('userRequested')
      ->with('supply:id,name')
      // ->with('room:id,name')
      // ->withWhereHas('room', fn($query) =>
      //   $query->whereIn('rooms.device_id', $assigned_devices)
      // )
      ->with('device', fn($query) =>
        $query->whereIn('id', $assigned_devices)
      )
      // ->with('media')
      ->where('done', $fixed)
      ->where(function ($q) use ( $search, $start, $end, $device_id ){

        if( $search ){
          $q->whereHas( 'supply', function($k) use ($search) {
              $k->where( 'name', 'like', "%{$search}%" );
          })
          ->orWhereHas( 'device', function($k) use ($search) {
            $k->where( 'name', 'like', "%{$search}%" );
          })
          ->orWhereHas( 'userRequested', function($k) use ($search) {
            $k->where( 'name', 'like', "%{$search}%" );
          });
        }

        if ( $start && $end ) {
          $q->whereBetween( 'date_requested', [$start, $end] );
        }

        if( $device_id ){
          $q->where('device_id', $device_id);
        }

      })
      ->get();

      $devices = $listings->pluck('device.name')->unique()->implode(', ');
      $period = $start . ' - ' . $end;
      $query = $search;

      $pdf = PDF::loadView('pages.pdfs.supplies', compact('listings', 'devices', 'period', 'query'))
      ->setOptions(['defaultFont' => 'sans-serif'])
      ->setPaper('a4', 'landscape');
      // return view('pages.pdfs.supplies', compact('listings', 'devices', 'period', 'query'));
      // return $pdf->stream('supplies.pdf');
      return $pdf->download('supplies.pdf');

    }
}
