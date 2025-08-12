<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\Record;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use App\Http\Traits\PlanTrait;
use App\Models\Contract;
use Auth;

class PlanController extends Controller
{
  use PlanTrait;

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
  // public function index()
  // {
  //     $this->authorize('manage', Plan::class);
  //     $this->authorize('viewAny', Device::class);

  //     $page_title = __('Plans');
  //     $page_description = __('Select a device first');
  //     $devices = Device::available()->get();
  //     $totalContract=Contract::count();
  //     $total_employees = Employee::where('function','!=',6)->count();
  //     $last_activities = Device::select('devices.id')
  //     ->selectRaw('MAX(records.updated_at) updated_at')
  //     ->leftJoin('records','records.device_id','=','devices.id')
  //     ->groupBy('id')
  //     ->get();

  //     return view('pages.plans.index', compact('page_title', 'page_description', 'devices', 'total_employees', 'last_activities'));

  // }

  public function index()
  {
    $this->authorize('manage', Plan::class);
    $this->authorize('viewAny', Device::class);

    $page_title = __('Plans');
    $page_description = __('Select a device first');
    $devices = Device::available()->get();

    // Count only valid employees based on contract logic
    $total_employees = Employee::withAllContractsSigned()
      ->where('function', '!=', 6)
      ->count();

    $totalContract = Contract::count();
    $last_activities = Device::select('devices.id')
      ->selectRaw('MAX(records.updated_at) updated_at')
      ->leftJoin('records', 'records.device_id', '=', 'devices.id')
      ->groupBy('devices.id')
      ->get();

    return view('pages.plans.index', compact(
      'page_title',
      'page_description',
      'devices',
      'total_employees',
      'last_activities'
    ));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $this->authorize('manage', Plan::class);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->authorize('manage', Plan::class);
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Plan  $plan
   * @return \Illuminate\Http\Response
   */

  // public function show(Device $device, Request $request)
  // {
  //   $this->authorize('manage', Plan::class);
  //   $this->authorize('viewAny', Device::class);

  //   $date = request('date') ? request('date') : Carbon::now();
  //   $inactive_employees = request('inactive') == 'true' ? true : false;

  //   try {
  //     $date = Carbon::parse($date);
  //   } catch (\Exception $e) {
  //     return redirect()->route('plans.index')->with(['error' => __('Date is not valid'), 'message' => ['exception' => $e->getMessage()]]);
  //     echo 'invalid date, enduser understands the error message';
  //   }

  //   //get devices that the user has access to
  //   $devices_arr = Device::available()->pluck('id')->toArray();

  //   $page_title = __('Plans');
  //   $page_description = __('Plans for ') . $date->translatedFormat('F Y');

  //   $cDate = new Carbon($date);
  //   $kDate = new Carbon($date);
  //   //$startDay->copy()->endOfDay();
  //   $from = $cDate->firstOfMonth();
  //   $to = $kDate->lastOfMonth();

  //   $period = CarbonPeriod::create($from, $to);
  //   $data = $device->employees()
  //     ->inDevices($devices_arr)
  //     ->where(function ($q) use ($from, $to, $inactive_employees) {
  //       if ($inactive_employees) {
  //         $q->inactiveBetween($from, $to);
  //       } else {
  //         $q->activeBetween($from, $to);
  //       }
  //     })
  //     ->with(['plans' => function ($q) use ($from, $to, $device) {
  //       $q->whereBetween('dita', [$from, $to])
  //         ->where('device_id', $device->id);
  //     }])
  //     ->with(['vacations' => function ($q) use ($from, $to) {
  //       $q->whereBetween('data', [$from, $to]);
  //     }])
  //     ->orderBy('function')
  //     ->orderBy('name')
  //     ->get();

  //   $holidays = Holiday::all();

  //   $today = date('Y-m-d');

  //   return view('pages.plans.show', compact('page_title', 'page_description', 'data', 'period', 'device', 'date', 'holidays', 'today', 'inactive_employees'));
  // }

  public function show(Device $device, Request $request)
  {
    $this->authorize('manage', Plan::class);
    $this->authorize('viewAny', Device::class);

    $date = $request->input('date', Carbon::now());
    $inactive_employees = $request->input('inactive') === 'true';

    try {
      $date = Carbon::parse($date);
    } catch (\Exception $e) {
      return redirect()->route('plans.index')->with([
        'error' => __('Date is not valid'),
        'message' => ['exception' => $e->getMessage()]
      ]);
    }

    $devices_arr = Device::available()->pluck('id')->toArray();

    $page_title = __('Plans');
    $page_description = __('Plans for ') . $date->translatedFormat('F Y');

    $from = Carbon::parse($date)->firstOfMonth();
    $to = Carbon::parse($date)->lastOfMonth();
    $period = CarbonPeriod::create($from, $to);

    $data = $device->employees()
      ->where(function ($query) {
        $query->where('employee_type', 0)
          ->orWhere(function ($q) {
            $q->where('employee_type', 1)
              ->whereRaw('
                            (SELECT COUNT(*) FROM contracts) = (
                                SELECT COUNT(*) 
                                FROM employee_contracts 
                                WHERE employee_contracts.employee_id = employees.id 
                                AND is_sign = 1
                            )');
          });
      })
      ->inDevices($devices_arr)
      ->where(function ($q) use ($from, $to, $inactive_employees) {
        if ($inactive_employees) {
          $q->inactiveBetween($from, $to);
        } else {
          $q->activeBetween($from, $to);
        }
      })
      ->with([
        'plans' => function ($q) use ($from, $to, $device) {
          $q->whereBetween('dita', [$from, $to])
            ->where('device_id', $device->id);
        },
        'vacations' => function ($q) use ($from, $to) {
          $q->whereBetween('data', [$from, $to]);
        }
      ])
      ->orderBy('function')
      ->orderBy('name')
      ->get();

    $holidays = Holiday::all();
    $today = date('Y-m-d');

    return view('pages.plans.show', compact(
      'page_title',
      'page_description',
      'data',
      'period',
      'device',
      'date',
      'holidays',
      'today',
      'inactive_employees'
    ));
  }


  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Plan  $plan
   * @return \Illuminate\Http\Response
   */
  public function edit(Plan $plan)
  {
    $this->authorize('manage', Plan::class);
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

    $plans_upsert = [];
    $plans_delete = [];

    foreach (request('plan') as $plan_key => $plan) {
      foreach ($plan as $day_key => $day) {
        if (isset($day['symbol']) && $day['symbol'] != null) {
          $day['symbol'] = $this->prepare_plan_symbol($day['symbol']);
          // $day['symbol'] = strtoupper( $day['symbol'] );
          array_push($plans_upsert, $day);
        } else {
          array_push($plans_delete, $day);
        }
      }
    }

    if (!empty($plans_delete)) {
      $values = [];
      foreach ($plans_delete as $plan) {
        $values[] = sprintf('(%s, "%s", "%s")', $plan['employee_id'], $plan['device_id'], $plan['dita']);
      }
      $query = "DELETE FROM plani WHERE (employee_id,device_id,dita) in (" . implode(', ', $values) . ")";
      DB::statement($query);
    }

    Plan::upsert(
      $plans_upsert,
      ['employee_id', 'device_id', 'dita'],
      ['symbol']
    );

    if ($request->ajax()) {
      return response()->json(['success' => __('Plans created/updated sucessfully')], 200);
    } else {
      return redirect()->back()->with(['success' => __('Plans created/updated sucessfully')]);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Plan  $plan
   * @return \Illuminate\Http\Response
   */
  public function destroy(Plan $plan)
  {
    $this->authorize('manage', Plan::class);
  }

  /**
   * Get the records between dates.
   *
   * @param  \App\Models\Device  $device
   * @return \Illuminate\Http\Response
   */
  public function records(Device $device, Request $request)
  {
    $this->authorize('manage', Plan::class);
    $this->authorize('viewAny', Device::class);

    $date = request('date') ? request('date') : Carbon::now();
    try {
      $date = Carbon::parse($date);
    } catch (\Exception $e) {
      return response()->json(['error' => __('Date is not valid')], 500);
    }

    $from = $date->firstOfMonth();
    $to = $from->copy()->lastOfMonth();
    if (Carbon::now()->endOfDay() < $to) {
      $to = Carbon::now()->endOfDay();
    }

    $records = Record::selectRaw('id, employee_id, DATE_FORMAT(time,"%Y-%m-%d") AS date')
      ->where('device_id', $device->id)
      ->whereBetween('time', [$from, $to])
      ->get();


    return response()->json($records, 200);
  }
}
