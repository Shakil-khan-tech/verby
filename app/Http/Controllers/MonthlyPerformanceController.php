<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Employee;
use App\Models\Record;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use App\Http\Traits\RecordTrait;

class MonthlyPerformanceController extends Controller
{
    use RecordTrait;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('manage', DailyReport::class);

        $page_title = __('Monthly Performance');
        $page_description = __('Select a device first');
        // $devices = Device::all();
        $devices = Device::available()->get();
        $total_employees = Employee::count();
        $last_activities = Device::select('devices.id')
        ->selectRaw('MAX(records.updated_at) updated_at')
        ->leftJoin('records','records.device_id','=','devices.id')
        ->groupBy('id')
        ->get();

        return view('pages.monthly_performance.index', compact('page_title', 'page_description', 'devices', 'total_employees', 'last_activities'));
    }

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
        $page_title = __('Monthly Performance for ') . $from->format('m.Y');
        $page_description = __('Device: ') . $device->name;

        $employee_records = Employee::select('id', 'name', 'surname','function')
        ->activeBetween($from, $to)
        ->with('records', function ($q) use ($from, $to, $device){
          $q->where('device_id', $device->id)
            ->whereBetween( 'time', [$from, $to] )
            ->orderBy('time', 'ASC');
        })
        ->whereHas('records', function ($q) use ($from, $to, $device){
          $q->where('device_id', $device->id)
            ->whereBetween( 'time', [$from, $to] );
        })
        ->orderBy('name', 'ASC')
        ->get();

        $period = CarbonPeriod::create($from, '1 day', $to);

        $matrix = collect();
        foreach ($employee_records as $key => $employee) {
          $matrix->push( $this->month_employee_matrix_aggregated( $period, $employee->records, $employee ) );
        }

        // return $matrix;

        return view('pages.monthly_performance.show', compact('page_title', 'page_description', 'device', 'month', 'matrix'));
    }
}
