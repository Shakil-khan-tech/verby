<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;

use App\Http\Traits\RecordTrait;

class DailyReportsController extends Controller
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
        $this->authorize('manage', DailyReport::class);

        $page_title = __('Daily Reports');
        $page_description = __('Select a device first');
        // $devices = Device::all();
        $devices = Device::available()->get();
        $total_employees = Employee::count();
        $last_activities = Device::select('devices.id')
        ->selectRaw('MAX(records.updated_at) updated_at')
        ->leftJoin('records','records.device_id','=','devices.id')
        ->groupBy('id')
        ->get();

        return view('pages.daily_reports.index', compact('page_title', 'page_description', 'devices', 'total_employees', 'last_activities'));
    }

    /**
     * Display the Daily Rooms for a device form the given date.
     *
     * @param  \App\Models\Device  $device
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device, Request $request)
    {
        $this->authorize('manage', DailyReport::class);

        $from = Carbon::now()->startOfDay(); //today - start of day
        // $middle = $from->copy()->endOfDay(); //today - end of day
        $to = $from->copy()->addDay()->endOfDay();  //add day for night shift, just in case he has
        
        if ( $request->has('date') ) {
          try {
              $from = Carbon::parse( request('date') )->startOfDay(); //today - start of day
            //   $middle = $from->copy()->endOfDay(); //today - end of day
              $to = $from->copy()->addDay()->endOfDay(); //add day for night shift, just in case he has
          } catch (\Exception $e) {
              // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
          }
        }
        // return $from . '-<br>-' . $to;
        $day = $from->format('d.m.Y');
        $page_title = __('Daily Reports for ') . $from->format('d.m.Y');
        $page_description = __('Device: ') . $device->name;

        $managers = $device->employees()
        ->where('function', 0)
        ->activeBetween($from, $to)
        ->get();

        $employees_func = Employee::whereHas('records')
        // the following block prevents cases where ex. day is 21.05.2022
        //and there are no records on 21st but there are on 22nd
        // THIS IS BUGGY ON PDF Calendar AS IT SKIPS RECORDS NEEDED
        // ->whereHas('records', function ($q) use($device, $from, $middle) {
        //     $q->whereBetween('records.time', [$from, $middle])
        //         ->where('device_id', $device->id);
        // })
        ->with([
          'records' => function ($record) use ($device, $from, $to) {
              return $record->whereBetween('records.time', [$from, $to])
                ->where('device_id', $device->id)
                ->with('calendar.rooms:id,name,category,depa_minutes,restant_minutes')
                ->orderBy('time', 'ASC');
          }
        ])
        ->whereHas('records', function ($q) use($device, $from, $to) {
            $q->whereBetween('records.time', [$from, $to])
                ->where('device_id', $device->id);
        })
        ->get()
        ->groupBy('function');

        /*matrix*/
        $daily_employees = $this->daily_employees_matrix( $employees_func, $from );
        // return $daily_employees;

        return view('pages.daily_reports.show', compact('page_title', 'page_description', 'device', 'day', 'employees_func', 'daily_employees', 'managers'));
    }
}
