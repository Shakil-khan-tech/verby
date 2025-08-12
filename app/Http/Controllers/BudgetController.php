<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Employee;
use App\Models\Record;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Traits\RecordTrait;
use App\Exports\BudgetPerformanceExport;
use Maatwebsite\Excel\Facades\Excel;

class BudgetController extends Controller
{
  use RecordTrait;

  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    $this->authorize('manage', DailyReport::class);

    $page_title = __('Budget Performance');
    $page_description = __('Select a device first');
    // $devices = Device::all();
    $devices = Device::available()->get();
    $total_employees = Employee::count();
    $last_activities = Device::select('devices.id')
      ->selectRaw('MAX(records.updated_at) updated_at')
      ->leftJoin('records', 'records.device_id', '=', 'devices.id')
      ->groupBy('id')
      ->get();

    return view('pages.budget.index', compact('page_title', 'page_description', 'devices', 'total_employees', 'last_activities'));
  }

  // public function show(Device $device, Request $request)
  // {
  //     $this->authorize('manage', DailyReport::class);

  //     $from = Carbon::now()->firstOfMonth();
  //     $to = $from->copy()->lastOfMonth();
  //     if ( $request->has('date') ) {
  //       try {
  //         $from = Carbon::parse( request('date') )->firstOfMonth()->startOfDay();
  //         $to = $from->copy()->lastOfMonth()->endOfDay();
  //       } catch (\Exception $e) {
  //           // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
  //       }
  //     }

  //     $month = $from->format('Y-m');
  //     $page_title = __('Monthly Performance for ') . $from->format('m.Y');
  //     $page_description = __('Device: ') . $device->name;

  //     $employee_records = Employee::select('id', 'name', 'surname')
  //     ->activeBetween($from, $to)
  //     ->with('records', function ($q) use ($from, $to, $device){
  //       $q->where('device_id', $device->id)
  //         ->whereBetween( 'time', [$from, $to] )
  //         ->orderBy('time', 'ASC');
  //     })
  //     ->whereHas('records', function ($q) use ($from, $to, $device){
  //       $q->where('device_id', $device->id)
  //         ->whereBetween( 'time', [$from, $to] );
  //     })
  //     ->orderBy('name', 'ASC')
  //     ->get();

  //     $period = CarbonPeriod::create($from, '1 day', $to);

  //     $matrix = collect();
  //     foreach ($employee_records as $key => $employee) {
  //       $matrix->push( $this->month_employee_matrix_aggregated( $period, $employee->records, $employee ) );
  //     }

  //     // return $matrix;

  //     return view('pages.budget.show', compact('page_title', 'page_description', 'device', 'month', 'matrix'));
  // }

  public function show(Device $device, Request $request)
  {
    $this->authorize('manage', DailyReport::class);

    $from = Carbon::now()->firstOfMonth();
    $to = $from->copy()->lastOfMonth();
    if ($request->has('date')) {
      try {
        $from = Carbon::parse(request('date'))->firstOfMonth()->startOfDay();
        $to = $from->copy()->lastOfMonth()->endOfDay();
      } catch (\Exception $e) {
        // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
      }
    }

    $month = $from->format('Y-m');
    $page_title = __('Budget Performance for ') .' '. $from->format('m.Y');
    $page_description = __('Device: ') . $device->name;

    $employee_records = Employee::select('id', 'name', 'surname', 'function')
      ->activeBetween($from, $to)
      ->with('records', function ($q) use ($from, $to, $device) {
        $q->where('device_id', $device->id)
          ->whereBetween('time', [$from, $to])
          ->orderBy('time', 'ASC');
      })
      ->whereHas('records', function ($q) use ($from, $to, $device) {
        $q->where('device_id', $device->id)
          ->whereBetween('time', [$from, $to]);
      })
      ->orderBy('name', 'ASC')
      ->get();

    $period = CarbonPeriod::create($from, '1 day', $to);
    $matrix = collect();
    foreach ($employee_records as $key => $employee) {
      $matrix->push($this->daily_employee_matrix_aggregated($period, $employee->records, $employee));
    }
    return view('pages.budget.show', compact('page_title', 'page_description', 'device', 'month', 'matrix', 'period'));
  }

  public function downloadExcel(Device $device, Request $request)
  {
    $this->authorize('manage', \App\Models\DailyReport::class);

    // Date handling (same as original)
    $from = Carbon::now()->firstOfMonth();
    $to = $from->copy()->lastOfMonth();

    if ($request->has('date')) {
      try {
        $from = Carbon::parse($request->date)->firstOfMonth()->startOfDay();
        $to = $from->copy()->lastOfMonth()->endOfDay();
      } catch (\Exception $e) {
        return response()->json(['message' => "Invalid date", 'error' => $e->getMessage()], 422);
      }
    }

    $month = $from->format('Y-m');
    $title = __('Budget Performance for ') . $from->format('m.Y');
    $description = __('Device: ') . $device->name;

    // Get employee records (same as original)
    $employee_records = Employee::select('id', 'name', 'surname', 'function')
      ->activeBetween($from, $to)
      ->with(['records' => function ($q) use ($from, $to, $device) {
        $q->where('device_id', $device->id)
          ->whereBetween('time', [$from, $to])
          ->orderBy('time', 'ASC');
      }])
      ->whereHas('records', function ($q) use ($from, $to, $device) {
        $q->where('device_id', $device->id)
          ->whereBetween('time', [$from, $to]);
      })
      ->orderBy('name', 'ASC')
      ->get();

    $period = CarbonPeriod::create($from, '1 day', $to);
    $matrix = collect();
    $functions = config('constants.functions');

    foreach ($employee_records as $employee) {
      $matrix->push($this->daily_employee_matrix_aggregated($period, $employee->records, $employee));
    }
    //echo "<pre>";print_r($matrix);exit;
    // Generate Excel file
    $export = new BudgetPerformanceExport(
      $device,
      $period,
      $matrix,
      $title,
      $description,
      $functions
    );

    //$filename = 'budget_performance_' . $device->id . '_' . $month . '.xlsx';
    $filename =  __('Budget Performance for ') .'_'. $device->id . '_' . $month . '.xlsx';

    return Excel::download($export, $filename);
  }

  // public function downloadExcel(Device $device, Request $request)
  // {
  //   $this->authorize('manage', \App\Models\DailyReport::class);

  //   $from = Carbon::now()->firstOfMonth();
  //   $to = $from->copy()->lastOfMonth();
  //   if ($request->has('date')) {
  //     try {
  //       $from = Carbon::parse($request->input('date'))->firstOfMonth()->startOfDay();
  //       $to = $from->copy()->lastOfMonth()->endOfDay();
  //     } catch (\Exception $e) {
  //       abort(500, 'Invalid date format');
  //     }
  //   }

  //   $month = $from->format('Y-m');
  //   $functions = config('constants.functions');
  //   $period = CarbonPeriod::create($from, '1 day', $to);

  //   $employee_records = Employee::select('id', 'name', 'surname', 'function')
  //     ->activeBetween($from, $to)
  //     ->with(['records' => function ($q) use ($from, $to, $device) {
  //       $q->where('device_id', $device->id)
  //         ->whereBetween('time', [$from, $to])
  //         ->orderBy('time', 'ASC');
  //     }])
  //     ->whereHas('records', function ($q) use ($from, $to, $device) {
  //       $q->where('device_id', $device->id)
  //         ->whereBetween('time', [$from, $to]);
  //     })
  //     ->orderBy('name', 'ASC')
  //     ->get();

  //   $matrix = collect();
  //   foreach ($employee_records as $employee) {
  //     $matrix->push($this->daily_employee_matrix_aggregated($period, $employee->records, $employee));
  //   }

  //   $pdf = Pdf::loadView('pages.budget.preview', compact('device', 'period', 'matrix', 'month', 'functions'))
  //     ->setPaper('A4', 'landscape'); // Use landscape for wide tables

  //   return $pdf->stream("budget-report-{$device->name}-{$month}.pdf");
  // }
}
