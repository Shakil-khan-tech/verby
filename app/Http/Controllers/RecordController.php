<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Employee;
use App\Models\Device;
use App\Models\Room;
use App\Models\Plan;
use App\Models\Calendar;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Validator;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\URL;
use App\Jobs\EmployeeRecordsJob;
use Illuminate\Support\Collection;
use App;

use App\Http\Traits\RecordTrait;

class RecordController extends Controller
{
    use RecordTrait;

    public function __construct()
    {
        $this->middleware('auth');
        // $this->authorizeResource('user');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', Record::class);

        $page_title = __('Records');
        if ( auth()->user()->hasRole(['super_admin', 'admin']) ) {
          $page_description = __('Latest records from devices');
        } else {
          $page_description = __('Showing records for: ');
          $page_description .= auth()->user()->devices->pluck('name')->implode(', ');
        }
        $employees = Employee::all();
        $devices = Device::all();

        return view('pages.records.index', compact('page_title', 'page_description', 'employees', 'devices'));
    }

    /**
     * Output records in json format.
     *
     * @return json
     */
    public function create()
    {
        $this->authorize('create', Record::class);

        $page_title = __('Add a record');
        $page_description = __('Create new');

        $devices = Device::all();

        return view('pages.records.create', compact('page_title', 'page_description', 'devices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Record::class);

        $request->validate([
          'employee' => 'required|exists:employees,id',
          'device' => 'required|exists:devices,id',
          'action' => 'required|numeric',
          'perform' => 'required|numeric',
          // 'identity' => 'required|numeric',
          'time' => 'required|date',
        ]);

        

        $record = new Record();
        $record->employee_id = request('employee');
        $record->device_id = request('device');
        $record->action = request('action');
        $record->perform = request('perform');
        $record->identity = 3; //PC
        $record->time = Carbon::parse(request('time'));
        $record->user_id = auth()->user()->is_device ? null : auth()->user()->id;
        // delete this test START
        $record->test_ipv4    = request()->ip();
        $record->test_user_id = auth()->user()->id;
        $record->test_type = 2;
        // delete this test END
        $record->save();

        try {
          if ( request('action') == 1 ) { //checkin
            $calendar = Calendar::where('date', Carbon::parse(request('time'))->format('Y-m-d'))->where('employee_id', request('employee'))->first();
            if ( !$calendar ) {
              return redirect()->back()->with([ 'error' => "Record saved but no rooms added. Reason: No rooms assigned for the given date -> " . Carbon::parse(request('time'))->format('Y-m-d'), 'message' => [] ]);
            }
  
            $record->calendar_id = $calendar->id;
            $record->save();
  
            $rooms = new Collection();
            if ( $request->has('depa') ) {
              $depa_obj = json_decode(request('depa'));
  
              if ( !empty($depa_obj) ) {
                foreach ($depa_obj as $room) {
                  $rooms->put( $room->room_id, [
                    'clean_type' => 0,
                    'extra' => $room->extra,
                    'status' => $room->status,
                    'volunteer' => isset($room->volunteer) && $room->status == 3 ? $room->volunteer : null,
                    'record_id' => $record->id
                  ]);
                }
              }
            }
  
            if ( $request->has('restant') ) {
              $restant_obj = json_decode(request('restant'));
              
              if ( !empty($restant_obj) ) {
                foreach ($restant_obj as $room) {
                  $rooms->put( $room->room_id, [
                    'clean_type' => 1,
                    'extra' => $room->extra,
                    'status' => $room->status,
                    'volunteer' => isset($room->volunteer) && $room->status == 3 ? $room->volunteer : null,
                    'record_id' => $record->id
                  ]);
                }
              }
            }

            $record->calendar->rooms()->syncWithoutDetaching( $rooms );
  
          }
          return redirect()->route('records.index')->with([ 'success' => __('controllers.record.store.success', ['id' => $record->id]) ]);
        } catch (\Exception $ex) {
          return redirect()->back()->with([ 'error' => __('Record cannot be created'), 'message' => ['exception' => $ex->getMessage()] ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function show(Record $record)
    {
        $this->authorize('manage', $record);

        // foreach ($record->rooms as $key => $room) {
        //   $room->pivot->volunteer_name = $room->pivot->volunteer_name;
        // }
        // return $record->rooms->where('pivot.clean_type', '=', 1)->where('pivot.status', '!=', 0);
        // return $record->calendar->rooms;
        // return $record->rooms;
        
        $page_title = __('Record');
        $page_description = __('Record for ') . $record->employee->name;

        return view('pages.records.show', compact('page_title', 'page_description', 'record'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function edit(Record $record)
    {
        $this->authorize('manage', $record);

        // foreach ($record->rooms as $key => $room) {
        //   $room->pivot->volunteer_name = $room->pivot->volunteer_name;
        // }

        $page_title = __('Record');
        $page_description = __('Record for ') . $record->employee->name;

        return view('pages.records.show', compact('page_title', 'page_description', 'record'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Record $record)
    {
        $this->authorize('manage', $record);

        $request->validate([
          'employee' => 'required|exists:employees,id',
          'device' => 'required|exists:devices,id',
          'action' => 'required|numeric',
          'perform' => 'required|numeric',
          // 'identity' => 'required|numeric',
          'time' => 'required|date',
        ]);

        $record->employee_id = request('employee');
        // $record->device_id = request('device');
        $record->action = request('action');
        $record->perform = request('perform');
        $record->identity = 3; //PC
        $record->time = Carbon::parse(request('time'));
        $record->user_id = auth()->user()->is_device ? null : auth()->user()->id;
        $record->save();        

        try {

          if ( request('action') == 1 ) { //checkin
            if ( $record->calendar_id == null ) {
              $calendar = Calendar::where('date', Carbon::parse(request('time'))->format('Y-m-d'))->where('employee_id', request('employee'))->first();
              if ( !$calendar ) {
                return redirect()->back()->with([ 'error' => "Record saved but no rooms added. Reason: No rooms assigned for the given date -> " . Carbon::parse(request('time'))->format('Y-m-d'), 'message' => [] ]);
              }
  
              $record->calendar_id = $calendar->id;
              $record->save();
            }
  
            $rooms = new Collection();
            if ( $request->has('depa') ) {
              $depa_obj = json_decode(request('depa'));
  
              if ( !empty($depa_obj) ) {
                foreach ($depa_obj as $room) {
                  $rooms->put( $room->room_id, [
                    'clean_type' => 0,
                    'extra' => $room->extra,
                    'status' => $room->status,
                    'volunteer' => isset($room->volunteer) && $room->status == 3 ? $room->volunteer : null,
                  ]);
                }
              }
            }
  
            if ( $request->has('restant') ) {
              $restant_obj = json_decode(request('restant'));
              
              if ( !empty($restant_obj) ) {
                foreach ($restant_obj as $room) {
                  $rooms->put( $room->room_id, [
                    'clean_type' => 1,
                    'extra' => $room->extra,
                    'status' => $room->status,
                    'volunteer' => isset($room->volunteer) && $room->status == 3 ? $room->volunteer : null,
                  ]);
                }
              }
            }
  
            foreach ($record->calendar->rooms as $key => $room) {
              if( $rooms->has($room->id) ){
                $room->pivot->clean_type = $rooms[$room->id]['clean_type'];
                $room->pivot->extra = $rooms[$room->id]['extra'];
                $room->pivot->status = $rooms[$room->id]['status'];
                $room->pivot->volunteer = $rooms[$room->id]['volunteer'];
                $room->pivot->record_id = $rooms[$room->id]['status'] != 0 ? $record->id : null;
                $room->pivot->save();
              } elseif ($room->pivot->record_id == $record->id) {
                //reset
                $room->pivot->status = 0;
                $room->pivot->volunteer = null;
                $room->pivot->record_id = null;
                $room->pivot->save();
              }
            }
            // $record->calendar->rooms()->syncWithoutDetaching( $rooms );
            
          }
          
          return redirect()->back()->with(['success' => __('Record updated successfully')]);
        } catch (\Exception $ex) {
          return redirect()->back()->with([ 'error' => __('Record cannot be updated'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Record  $record
     * @return \Illuminate\Http\Response
     */
    public function destroy(Record $record, Request $request)
    {
        $this->authorize('manage', $record);

        $r = Record::findOrFail($record->id);
        $r->delete();

        // redirect
        if( $request->ajax() ){
            return response()->json( ['success' => __('Record deleted successfully')], 200 );
        }
        else {
            return redirect()->route('records.index')->with(['success' => __('Record deleted successfully')]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajax(Request $request)
    {
        $this->authorize('viewAny', Record::class);

        $devices_arr = Device::available()->pluck('id');
        

        // return $request->all();

        if ( !$request->ajax() ) {
          // return response()->json(['message' => "Just ajax requests!" ], 500);
        }
        // defaults
        $page = 1;
        $perpage = 10;
        $order_by = 'updated_at';
        $sort = 'asc';


        if ( $request->has('pagination') ) {
          if ( !empty( $request->get('pagination')['page'] ) ) {
            $page = $request->get('pagination')['page'];
          }
          if ( !empty( $request->get('pagination')['perpage'] ) ) {
            $perpage = $request->get('pagination')['perpage'];
          }
        }

        if ( $request->has('sort') ) {
          if ( is_array( request('sort') ) ) {
            $order_by = request('sort')['field'];
            $sort = request('sort')['sort'];
          }
        }

        if ( $request->has('query') ) {

          $search = $request->get('query');
          if ( !is_array($search) ) {
            $records = Record::with('employee:id,name,surname,function')
            ->with('user')
            ->whereHas('device', function ($query) use ($devices_arr) {
              if ( $devices_arr ) {
                $query->whereIn('id', $devices_arr);
              }
            })
            ->with('device:id,name')
            ->with('calendar.rooms:id,name,category')
            ->orderBy($order_by, $sort)
            ->paginate( $perpage, ['*'], null, $page );
          } else {
            $records = Record::with('employee:id,name,surname,function')
            ->whereHas('device', function ($query) use ($devices_arr) {
              if ( $devices_arr ) {
                $query->whereIn('id', $devices_arr);
              }
            })
            ->with('device:id,name')
            ->with('calendar.rooms:id,name,category')
            ->orderBy($order_by, $sort)
            ->where(function ($q) use ($search){
              foreach( $search as $key => $value ) {
                if( $key == "generalSearch" ){
                    $q->whereHas( 'employee', function($k) use ($value) {
                       // $k->where( 'name', 'like', "%{$value}%" );
                       $k->whereRaw( "CONCAT(name, ' ', surname) LIKE ?", ['%'.$value.'%'] );
                    });
                }

                if( $key == "Date" ){
                  if ( !empty( $value['start'] || $value['end'] ) ) {
                    $start = new Carbon( $value['start'] );
                    // $start = $start->startOfDay();
                    $end = new Carbon( $value['end'] );
                    $end = $end->endOfDay();
                    $q->whereBetween( 'time', [$start, $end] );
                  }
                }

                if( $key == "device" ){
                  $q->where( 'device_id', $value );
                }

                // elseif ( $value != null ) {
                //     $q->where( $key, 'like', "%{$value}%" );
                // }
              }
            })->paginate( $perpage, ['*'], null, $page );
          }

        } else {

          $records = Record::with('employee:id,name,surname,function')
          ->with('user')
          ->whereHas('device', function ($query) use ($devices_arr) {
            if ( $devices_arr ) {
              $query->whereIn('id', $devices_arr);
            }
          })
          ->with('device:id,name')
          ->with('calendar.rooms:id,name,category')
          ->orderBy($order_by, $sort)
          ->paginate( $perpage, ['*'], null, $page );
        }

        // $records->map(function ($record) {
        //   $record->rooms->map(function ($room) {
        //     $room->volunteer = $room->pivot->volunteer;
        //     return $room;
        //   });        
        //     return $record;
        // });

        // return $records;


        $meta = [
          "page" => $records->currentPage(),
          "pages" => intval(count($records) / $perpage),
          "perpage" => $perpage,
          "total" => $records->total(),
          "sort" => $sort,
          "field" => $order_by,
        ];

        return response()->json( ['meta' => $meta, 'data' => $records->items()], 200 );
        // return response()->json( $records, 200 );
    }

    /**
     * Get all employees in json format.
     *
     * @return json
     */
    public function employees(Request $request)
    {
        $this->authorize('viewAny', Employee::class);

        if ( $request->has('single_employee') ) {
          return response()->json( Employee::findOrFail( request('single_employee') ), 200 );
        }

        $page = 1; //default
        $perpage = 30; //default

        if ( $request->has('page') ) {
          $page = $request->get('page');
        }
        if ( $request->has('perpage') ) {
          $perpage = $request->get('perpage');
        }

        if ( $request->has('query') ) {

          $search = $request->get('query');
          $employees = Employee::where(function ($q) use ($search){
            $q->whereRaw( "CONCAT(name, ' ', surname) LIKE ?", ['%'.$search.'%'] );
          })->paginate( $perpage, ['*'], null, $page );

        } else {
          $employees = Employee::paginate( $perpage, ['*'], null, $page );
        }

        $data = [
          "total_count" => $employees->total(),
          "items" => $employees->items(),
        ];

        // return response()->json( ['meta' => $meta, 'data' => $employees], 200 );
        return response()->json( $data, 200 );
    }

    /**
     * Get all rooms in json format.
     *
     * @return json
     */
    public function rooms(Record $record, Request $request)
    {
        $this->authorize('viewAny', Device::class);

        $date = $employee = $from_calendar = null;
        if ( $request->has('date') ) { $date = Carbon::parse( $request->get('date') )->format('Y-m-d'); }
        if ( $request->has('employee') ) { $employee = $request->get('employee'); }
        if ( $request->has('calendar') && $request->get('calendar') == 'true' ) { $from_calendar = true; }

        if ( $record->exists ) {
          $rooms = $from_calendar && $record->calendar ? $record->calendar->rooms->groupBy('category') : $record->rooms->groupBy('category');
        } elseif ( $date && $employee ) {
          // find calendar
          $calendar = Calendar::where('date', $date)->where('employee_id', $request->get('employee'))->first();
          $rooms = $calendar ? $calendar->rooms->groupBy('category') : collect();
        } else {
          $rooms = collect();
        }        

        return response()->json( $rooms, 200 );
    }


    /**
     * Index for calendar records for an employee.
     *
     * @return json
     */
    public function calendar_index()
    {
        $this->authorize('viewAny', Employee::class);
        $this->authorize('viewAny', Record::class);

        $page_title = __('Record Calendar');
        $page_description = __('Select an employee first');
        // $employees = Employee::all();
        $devices = Device::all();

        return view('pages.records.calendar-index', compact('page_title', 'page_description', 'devices'));
    }

    /**
     * Get records for an employee.
     *
     * @return json
     */
    public function calendar_show(Employee $employee, Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $this->authorize('viewAny', Record::class);

        // return $records;
        // $record = Record::with('rooms')->findOrFail($record->id);
        $page_title = $employee->fullname;
        // $page_description = 'Record for' . $record->employee->name;
        $page_description = __('Record Calendar');
        $devices = Device::all();


        $calendar_time = $request->has('time') ? request('time') : 'null';

        return view('pages.records.calendar-show', compact('page_title', 'page_description', 'employee', 'devices', 'calendar_time'));
    }

    /**
     * Get records for an employee.
     *
     * @return json
     */
    public function calendarAjax(Employee $employee, Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $this->authorize('viewAny', Record::class);

        $validator = Validator::make($request->all(), [
          'start' => 'required|numeric',
          'end' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all() ], 500);
        }

        $start = Carbon::createFromTimestampMs(request('start'), 'Europe/Zurich')->toDateTimeString();
        $end = Carbon::createFromTimestampMs(request('end'), 'Europe/Zurich')->toDateTimeString();

        $records = Record::where('employee_id', $employee->id)
        ->where(function ($q) use ($start, $end){
          $q->whereBetween( 'time', [$start, $end] );
        })
        ->select('id', 'device_id', 'action', 'time', 'perform', 'identity', 'calendar_id')
        ->with('device')
        ->with('calendar.rooms:id,name,category')
        ->orderBy('time', 'ASC')
        ->get();

        
        $plans = Plan::where('employee_id', $employee->id)
        ->where(function ($q) use ($start, $end){
          $q->whereBetween( 'dita', [$start, $end] )
            // ->whereIn( 'symbol', ['K','U','F','W','S','KK','O','UN','V','FR','SC'] );
            ->whereIn( 'symbol', ['F','W','S','A','K','KK','O','U','V','FR','SC','MSE','VSE','UN'] );
        })
        ->select('id', 'symbol', 'dita')
        ->get();

        $period = CarbonPeriod::create($start, '1 day', $end);
        $matrix = $this->month_employee_matrix($period, $records);
        foreach ($matrix as $day => $emp_records) {
          $seconds[ Carbon::parse($day)->format('Y-m-d') ] = $this->calculate_hours($emp_records);
        }

        $data = [
          'records' => $records,
          'plans' => $plans,
          'seconds' => $seconds,
        ];
        

        return response()->json( $data, 200 );
    }

    /**
     * Store or update a Record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function calendarStoreOrUpdate(Employee $employee, Request $request)
    {
        $this->authorize('create', Record::class);

        $request->validate([
          'record_id' => 'required',
          // 'employee' => 'required|exists:employees,id',
          'device' => 'required|exists:devices,id',
          'action' => 'required|numeric',
          'perform' => 'required|numeric',
          // 'identity' => 'required|numeric',
          '_day' => 'required|date',
          '_time' => 'required|date_format:H:i:s',
        ]);
        $date = request('_day') . " " . request('_time');


        $record = Record::findOrNew( request('record_id') );

        $record->employee_id = $employee->id;
        $record->device_id = request('device');
        $record->action = request('action');
        $record->perform = request('perform');
        $record->identity = 3; //PC
        $record->time = Carbon::parse( $date );
        $record->user_id = auth()->user()->is_device ? null : auth()->user()->id;
        $record->save();        

        try {
          
          if ( request('action') == 1 ) { //checkin
            $calendar = Calendar::where('date', Carbon::parse($date)->format('Y-m-d'))->where('employee_id', $employee->id)->first();
            if ( !$calendar ) {
              return response()->json( ['success' => __('Record updated successfully, but no rooms added. <br>Reason: No rooms assigned for the given date')], 200 );
            }
  
            $record->calendar_id = $calendar->id;
            $record->save();
  
            $rooms = new Collection();
            if ( $request->has('depa') ) {
              $depa_obj = json_decode(request('depa'));
  
              if ( !empty($depa_obj) ) {
                foreach ($depa_obj as $room) {
                  $rooms->put( $room->room_id, [
                    'clean_type' => 0,
                    'extra' => $room->extra,
                    'status' => $room->status,
                    'volunteer' => isset($room->volunteer) && $room->status == 3 ? $room->volunteer : null,
                    // 'record_id' => $record->id
                  ]);
                }
              }
            }
  
            if ( $request->has('restant') ) {
              $restant_obj = json_decode(request('restant'));
              
              if ( !empty($restant_obj) ) {
                foreach ($restant_obj as $room) {
                  $rooms->put( $room->room_id, [
                    'clean_type' => 1,
                    'extra' => $room->extra,
                    'status' => $room->status,
                    'volunteer' => isset($room->volunteer) && $room->status == 3 ? $room->volunteer : null,
                    // 'record_id' => $record->id
                  ]);
                }
              }
            }
  
            // Relation not ready, fetch it.
            // $record = Record::where('id', $record->id)->first();
  
            foreach ($record->calendar->rooms as $key => $room) {
              if( $rooms->has($room->id) ){
                $room->pivot->clean_type = $rooms[$room->id]['clean_type'];
                $room->pivot->extra = $rooms[$room->id]['extra'];
                $room->pivot->status = $rooms[$room->id]['status'];
                $room->pivot->volunteer = $rooms[$room->id]['volunteer'];
                $room->pivot->record_id = $rooms[$room->id]['status'] != 0 ? $record->id : null;
                $room->pivot->save();
              } elseif ($room->pivot->record_id == $record->id) {
                //reset
                $room->pivot->status = 0;
                $room->pivot->volunteer = null;
                $room->pivot->record_id = null;
                $room->pivot->save();
              }
            }
            // $record->calendar->rooms()->syncWithoutDetaching( $rooms );
          }
          
          return response()->json( ['success' => __('Record updated successfully')], 200 );
        } catch (\Exception $ex) {
          return response()->json(['message' => __('Record cannot be created'), 'errors' => ['exception' => $ex->getMessage()] ], 500);
        }

    }

    /**
     * Delete a Record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function calendarDelete(Request $request)
    {
        $this->authorize('create', Record::class);

        $request->validate([
          'record_id' => 'required',
        ]);

        $record = Record::find( request('record_id') );

        try {
          if ( $record ) {
            $record->delete();
          }
          return response()->json( ['success' => __('Record deleted successfully')], 200 );
        } catch (\Exception $ex) {
          return response()->json(['message' => __('Record cannot be deleted'), 'errors' => ['exception' => $ex->getMessage()] ], 500);
        }

    }

    /**
     * Get records for an employee.
     *
     * @return json
     */
    public function calendarPrint(Employee $employee, Request $request)
    {
      
        $this->authorize('viewAny', Employee::class);
        $this->authorize('viewAny', Record::class);

        $from = Carbon::now()->firstOfMonth()->startOfDay();
        $to = $from->copy()->lastOfMonth()->endOfDay();
        if ( $request->has('date') ) {
          try {
              $from = Carbon::parse( request('date') )->firstOfMonth()->startOfDay();
              $to = $from->copy()->lastOfMonth()->endOfDay();
          } catch (\Exception $e) {
              // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
          }
        }
        // return $to;
        $month = $from->format('Y-m');
        $page_title = __('Individual Monthly Performance for ') . $from->format('m.Y');
        $page_description = __('Employee') . ': '. $employee->fullname;
        $page_description .= ($employee->PartTime == 1) ? ' (Hourly)' : ' (Monthly)';
        $devices = Device::all();


        $employee_records = Record::where('employee_id', $employee->id)
        // ->whereBetween( 'time', [$start, $end] )
        ->where(function ($q) use ($from, $to){
          $q->whereBetween( 'time', [$from, $to] );
        })
        ->select('id', 'device_id', 'action', 'time', 'perform', 'identity', 'calendar_id')
        ->with('device')
        ->with('calendar.rooms:id,name,category,depa_minutes,restant_minutes')
        ->orderBy('time', 'ASC')
        ->get();

        $period = CarbonPeriod::create($from, '1 day', $to);
        /*matrix*/
        $matrix = $this->month_employee_matrix($period, $employee_records);
        $plans = Plan::where('employee_id', $employee->id)->whereBetween( 'dita', [$from, $to] )->select('id', 'symbol')->get();
        // $plans = Plan::where('employee_id', $employee->id)->whereBetween( 'dita', [$from, $to] )->whereIn( 'symbol', ['K','U','F'] )->select('id', 'symbol')->get();

        $plans = Plan::where('employee_id', $employee->id)
        ->where(function ($q) use ($from, $to){
          $q->whereBetween( 'dita', [$from, $to] );
            // ->whereIn( 'symbol', ['K','U','F'] );
        })
        ->select('id', 'symbol', 'dita')
        ->get();
        
        $plans->map(function ($plan) {
          $plan->dita_formatted = Carbon::parse($plan->dita)->format('d.m.Y');
          return $plan;
        });

        // get employee response for the month, if exists
        $employee_response = $employee->calendar_report_feedback->whereBetween('date', [$from->format('Y-m-d'), $to->format('Y-m-d')])->sortBy('created_at')->last()?->response;

        return view('pages.records.print', compact('page_title', 'page_description', 'employee_records', 'period', 'month', 'matrix', 'employee', 'devices', 'plans', 'employee_response'));
    }

    /**
     * Display a view for selecting user.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarReport(Employee $employee, Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $this->authorize('viewAny', Record::class);

        $page_title = __('Individual Monthly Performance');
        $page_description = __('Select an employee first');
        $devices = Device::all();

        return view('pages.records.calendar-report', compact('page_title', 'page_description', 'devices'));
    }

    /**
     * Send an email to employee with records for the month.
     *
     * @return json
     */
    public function calendarEmail(Employee $employee, Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $this->authorize('viewAny', Record::class);

        if ( !$request->ajax() ) {
          return response()->json(['message' => "Just ajax requests!" ], 500);
        }

        $validator = Validator::make(['email' => $employee->email],[
          'email' => 'required|email'
        ]);
        
        if( $validator->fails() ){
          return response()->json(['message' => __('Employee email is not valid or is empty'), 'errors' => ['exception' => $validator->errors()] ], 500);
        }

        $from = Carbon::now()->firstOfMonth();
        $to = $from->copy()->lastOfMonth();
        if ( $request->has('date') ) {
          try {
              $from = Carbon::parse( request('date') )->firstOfMonth();
              $to = $from->copy()->lastOfMonth();
          } catch (\Exception $e) {
              return response()->json(['message' => __('Email not sent. Date is not valid'), 'errors' => ['exception' => $e->getMessage()] ], 500);
          }
        }
        $month = $from->format('Y-m');
        $expiration = Carbon::now()->addDays(15)->format('d.m.Y H:i:s');
        $url = URL::signedRoute('external.records_report', ['employee' => $employee->id, 'date' => $month, 'expiration' => $expiration]);
        $locale = app()->getLocale();

        EmployeeRecordsJob::dispatch($url, $month, $employee->email, $expiration, $employee, $locale )->delay(now()->addSeconds(1));
        
        return response()->json( ['success' => __('Email sent successfully')], 200 );
    }

    /**
     * Send an email to multiple employee's with records for the month
     *
     * @return json
     */
    public function calendarEmailBulk(Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        $this->authorize('viewAny', Record::class);

        if ( !$request->ajax() ) {
          return response()->json(['message' => "Just ajax requests!" ], 500);
        }

        $validator = Validator::make($request->all(),[
          'employees' => 'required|array',
          'employees.*' => 'required|exists:employees,id',
        ]); 
        
        if( $validator->fails() ){
          return response()->json(['message' => __('Employee email is not valid or is empty'), 'errors' => ['exception' => $validator->errors()] ], 500);
        }

        $from = Carbon::now()->firstOfMonth();
        $to = $from->copy()->lastOfMonth();
        if ( $request->has('date') ) {
          try {
              $from = Carbon::parse( request('date') )->firstOfMonth();
              $to = $from->copy()->lastOfMonth();
          } catch (\Exception $e) {
              return response()->json(['message' => __('Email not sent. Date is not valid'), 'errors' => ['exception' => $e->getMessage()] ], 500);
          }
        }

        $month = $from->format('Y-m');
        $expiration = Carbon::now()->addDays(15)->format('d.m.Y H:i:s');
        $employees = Employee::whereIn('id', request('employees'))->whereNotNull('email')->get(); // filter employees that have email
        $locale = app()->getLocale();

        foreach ($employees as $employee) {
          $url = URL::signedRoute('external.records_report', ['employee' => $employee->id, 'date' => $month, 'expiration' => $expiration]);
          EmployeeRecordsJob::dispatch($url, $month, $employee->email, $expiration, $employee, $locale)->delay(now()->addSeconds(1));
        }
        
        return response()->json( ['success' => __( $employees->count() . ' emails sent successfully')], 200 );
    }

}