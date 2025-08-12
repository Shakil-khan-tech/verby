<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Employee;
use App\Models\Record;
use App\Models\MonthlyReport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Auth;
use DB;


use App\Http\Traits\RecordTrait;

class MonthlyReportsController extends Controller
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

        $page_title = __('Monthly Reports');
        $page_description = __('Select a device first');
        $devices = Device::available()->get();
        $total_employees = Employee::count();
        $last_activities = Device::select('devices.id')
        ->selectRaw('MAX(records.updated_at) updated_at')
        ->leftJoin('records','records.device_id','=','devices.id')
        ->groupBy('id')
        ->get();

        return view('pages.monthly_reports.index', compact('page_title', 'page_description', 'devices', 'total_employees', 'last_activities'));
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

        $from = Carbon::now()->firstOfMonth();
        $to = $from->copy()->lastOfMonth();
        if ( $request->has('date') ) {
          try {
              $from = Carbon::parse( request('date') )->firstOfMonth()->startOfDay();
              $to = $from->copy()->lastOfMonth()->endOfDay();
          } catch (\Exception $e) {
              // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
          }
        }

        $month = $from->format('Y-m');
        $page_title = __('Monthly Reports for ') . $from->format('m.Y');
        $page_description = __('Device: ') . $device->name;

        $records = Record::where('device_id', $device->id)
        ->where(function ($q) use ($from, $to){
          $q->whereBetween( 'time', [$from, $to] )
          ->where('action', 1);
        })
        ->with('calendar.rooms:id,name,category')
        ->with('employee')
        ->orderBy('time', 'ASC')
        // ->groupBy('records.employee_id')
        ->get();
        // return $records;
        

        $employees = Employee::whereHas('records')
        ->with([
            'records' => function ($record) use ($device, $from, $to) {
                return $record->whereBetween('records.time', [$from, $to])
                ->where('device_id', $device->id)
                ->with('calendar.rooms:id,name,category')
                ->orderBy('time', 'ASC');
            }
        ])
        ->whereHas('records', function ($q) use($device, $from, $to) {
            $q->whereBetween('records.time', [$from, $to])
                ->where('device_id', $device->id);
        })
        ->get();

        // return $employees;


        // return $employees[0]->records;

        // return $records;
        
        $reports = MonthlyReport::where('device_id', $device->id)
        ->where(function ($q) use ($from, $to){
            $q->whereBetween( 'date', [$from, $to] );
        })
        ->orderBy('date', 'ASC')
        ->get();


        $period = CarbonPeriod::create($from, '1 day', $to);
        /*matrix*/
        $matrix = $this->monthly_records_matrix($period, $records, $reports, $employees);
        // return $matrix;

        return view('pages.monthly_reports.show', compact('page_title', 'page_description', 'device', 'month', 'matrix'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Plan  $plan
     * @return \Illuminate\Http\Response
     */
    public function update(Device $device, Request $request)
    {
        $this->authorize('manage', Plan::class);
        $this->authorize('viewAny', Device::class);

        $report_upsert = [];

        foreach (request('report') as $day => $report) {
            $report['date'] = Carbon::parse( $day );
            $report['device_id'] = $device->id;
            array_push($report_upsert, $report);
        }

        MonthlyReport::upsert(
            $report_upsert,
            ['device_id', 'date'],
            ['date', 'reg', 'rote']
        );

        if( $request->ajax() ){
            return response()->json(['success' => __('Report saved sucessfully')], 200);
        } else {
            return redirect()->back()->with( ['success' => __('Report saved sucessfully')] );
        }
    }
}
