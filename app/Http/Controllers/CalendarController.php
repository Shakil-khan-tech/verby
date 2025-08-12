<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\Room;
use App\Models\Plan;
use App\Models\Device;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\CalendarReportResponse;
use App\Models\Contract;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use Validator;
use Auth;

class CalendarController extends Controller
{
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
  //     $this->authorize('manage', Calendar::class);
  //     $this->authorize('viewAny', Device::class);

  //     $page_title = __('Calendars');
  //     $page_description = __('Select a device first');
  //     // $devices = Device::all();
  //     $devices = Device::available()->get();
  //     $total_employees = Employee::where('function','!=',6)->count();
  //     $last_activities = Device::select('devices.id')
  //     ->selectRaw('MAX(records.updated_at) updated_at')
  //     ->leftJoin('records','records.device_id','=','devices.id')
  //     ->groupBy('id')
  //     ->get();

  //     return view('pages.calendars.index', compact('page_title', 'page_description', 'devices', 'total_employees', 'last_activities'));

  // }

  public function index()
  {
    $this->authorize('manage', Calendar::class);
    $this->authorize('viewAny', Device::class);

    $page_title = __('Calendars');
    $page_description = __('Select a device first');

    $devices = Device::available()->with('validEmployees')->get();

    $query = Employee::query();
    if (config('app.plan_contracts_gating', env('PLAN_CONTRACTS_GATING', false))) {
      $query = $query->withAllContractsSigned();
    }
    $total_employees = $query->where('function', '!=', 6)->count();

    $last_activities = Device::select('devices.id')
      ->selectRaw('MAX(records.updated_at) updated_at')
      ->leftJoin('records', 'records.device_id', '=', 'devices.id')
      ->groupBy('devices.id')
      ->get();

    return view('pages.calendars.index', compact(
      'page_title',
      'page_description',
      'devices',
      'total_employees',
      'last_activities'
    ));
  }


  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Plan  $plan
   * @return \Illuminate\Http\Response
   */
  // public function show(Device $device, Request $request)
  // {
  //   $this->authorize('manage', Calendar::class);
  //   $this->authorize('viewAny', Device::class);

  //   $date = request('date') ? request('date') : Carbon::now();
  //   try {
  //     $date = Carbon::parse($date);
  //   } catch (\Exception $e) {
  //     return redirect()->route('calendars.index')->with(['error' => __("Date is not valid"), 'message' => ['exception' => $e->getMessage()]]);
  //   }

  //   // test
  //   // $calendar = Calendar::where('date', $date)->get();
  //   // return $calendar;
  //   // test


  //   $page_title = __('Calendar for ') . $date->format('F Y');
  //   $page_description = __('Device: ') . $device->name;

  //   // $cDate = new Carbon($date);
  //   $from = $date->firstOfMonth();
  //   $to = $date->copy()->lastOfMonth();

  //   $rooms = Room::where('device_id', $device->id)->get();
  //   $rooms = $rooms->groupBy('category');

  //   $period = CarbonPeriod::create($from, $to);

  //   $data = $device->employees()
  //     ->activeBetween($from, $to)
  //     ->with(['calendars' => function ($q) use ($from, $to) {
  //       $q->whereBetween('date', [$from, $to]);
  //     }])
  //     ->orderBy('function')
  //     ->orderBy('name')
  //     ->get();

  //   $holidays = Holiday::all();

  //   $today = date('Y-m-d');

  //   return view('pages.calendars.show', compact(
  //     'page_title',
  //     'page_description',
  //     'data',
  //     'period',
  //     'device',
  //     'date',
  //     'rooms',
  //     'today',
  //     'holidays'
  //   ));
  // }

  public function show(Device $device, Request $request)
  {
    $this->authorize('manage', Calendar::class);
    $this->authorize('viewAny', Device::class);

    $date = $request->input('date', Carbon::now());

    try {
      $date = Carbon::parse($date);
    } catch (\Exception $e) {
      return redirect()->route('calendars.index')->with([
        'error' => __("Date is not valid"),
        'message' => ['exception' => $e->getMessage()]
      ]);
    }

    $page_title = __('Calendar for ') . $date->format('F Y');
    $page_description = __('Device: ') . $device->name;

    $from = $date->copy()->firstOfMonth();
    $to = $date->copy()->lastOfMonth();
    $period = CarbonPeriod::create($from, $to);

    $rooms = Room::where('device_id', $device->id)->get()->groupBy('category');
    $holidays = Holiday::all();
    $today = date('Y-m-d');

    // Get all contract IDs for validation
    $allContractIds = Contract::pluck('id')->toArray();

    // Get employee IDs linked via device_employee table
    $employeeIds = DB::table('device_employee')
      ->where('device_id', $device->id)
      ->pluck('employee_id');

    // Filter employees
    $empQuery = Employee::whereIn('id', $employeeIds);
      if (config('app.plan_contracts_gating', env('PLAN_CONTRACTS_GATING', false))) {
        $empQuery = $empQuery->where(function ($query) use ($allContractIds) {
          $query->where('employee_type', 0)
            ->orWhere(function ($q) use ($allContractIds) {
              $q->where('employee_type', 1)
                ->whereRaw('
                              (SELECT COUNT(*) FROM contracts) = (
                                  SELECT COUNT(*) 
                                  FROM employee_contracts 
                                  WHERE employee_contracts.employee_id = employees.id 
                                  AND is_sign = 1
                              )');
            });
        });
      }
      $data = $empQuery
        ->activeBetween($from, $to)
        ->with(['calendars' => function ($q) use ($from, $to) {
          $q->whereBetween('date', [$from, $to]);
        }])
        ->orderBy('function')
        ->orderBy('name')
        ->get();

    return view('pages.calendars.show', compact(
      'page_title',
      'page_description',
      'data',
      'period',
      'device',
      'date',
      'rooms',
      'today',
      'holidays'
    ));
  }


  /**
   * Get calendars in json format.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function load(Device $device, Request $request)
  {
    $this->authorize('manage', Calendar::class);
    $this->authorize('viewAny', Device::class);

    $date = request('date') ? request('date') : Carbon::now();
    try {
      $date = Carbon::parse($date);
    } catch (\Exception $e) {
      return response()->json(['error' => "Date is not valid!"], 500);
    }

    $cDate = new Carbon($date);
    $from = $cDate->firstOfMonth();
    $to = $cDate->copy()->lastOfMonth();

    // return $from;

    $data = $device->employees()
      ->select('id', 'name', 'surname')
      ->activeBetween($from, $to)
      ->with(['calendars' => function ($q) use ($device, $from, $to) {
        $q->whereBetween('calendars.date', [$from, $to])
          ->where('calendars.device_id', $device->id);
        $q->with('rooms');
      }])
      // ->whereHas('calendars', function ($q) use($device, $from, $to) {
      //     $q->whereBetween('calendars.date', [$from, $to])
      //         ->where('calendars.device_id', $device->id);
      // })
      // ->with('calendars.rooms')
      ->orderBy('function')
      ->orderBy('name')
      ->get();

    return response()->json($data, 200);
  }

  /**
   * Get calendar for a single day, single employee in json format.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function loadSingleDay(Device $device, Request $request)
  {
    $this->authorize('manage', Calendar::class);
    $this->authorize('viewAny', Device::class);

    //validation
    $validator = Validator::make($request->all(), [
      'employee' => 'required',
      'date' => 'required',
      // 'clean_type' => 'required',
      // 'device' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()->all()], 500);
    }

    // $clean_type = request('clean_type');

    //unselected rooms only
    // $busy_rooms = Room::where('device_id', $device->id)
    // ->whereHas('calendars', function ($q) use($clean_type) {
    //   $q->where('date', request('date'));
    //     // ->where('employee_id', request('employee'));
    // })
    // ->with(['calendars' => function ($q) {
    //   $q->where('date',  request('date'));
    //   $q->with('employee');
    // }])
    // // ->with('calendars.employee')
    // ->get();
    // $busy_rooms = $rooms->groupBy('category');
    // return $busy_rooms;

    $rooms = Room::where('device_id', $device->id)
      ->whereDoesntHave('calendars', function ($q) use ($device)  /*use($clean_type)*/ {
        $q->where('date', [request('date')])
          ->where('employee_id', [request('employee')])
          // ->where('calendar_room.status', '!=', 1)
          ->where('device_id', $device->id);
      })
      ->with(['calendars' => function ($q) use ($device) {
        $q->where('date',  request('date'));
        // $q->where('calendar_room.status', '!=', 1);
        $q->where('device_id', $device->id);
        $q->with('employee');
      }])
      ->get()
      ->groupBy('category');


    $calendar = Calendar::with('rooms')
      ->where('device_id', $device->id)
      ->where('employee_id', request('employee'))
      ->where('date', request('date'))
      ->with(['rooms' => function ($q) /*use($clean_type)*/ {
        // $q->where('clean_type', $clean_type);
      }])
      ->get();

    $employee = Employee::select('id', 'name', 'surname')->where('id', request('employee'))->first();

    return response()->json(['calendar' => $calendar, 'employee' => $employee, 'rooms' => $rooms], 200);
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
    $this->authorize('manage', Calendar::class);
    $this->authorize('viewAny', Device::class);

    //validation
    $validator = Validator::make($request->all(), [
      'date' => 'required',
      'employee' => 'required',
      // 'clean_type' => 'required',
    ]);
    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()->all()], 500);
    }

    $calendar = Calendar::firstOrCreate(
      ['device_id' => $device->id, 'employee_id' => request('employee'), 'date' => request('date')],
      ['user_id'   => Auth::user()->id]
    );

    // return $calendar->rooms();

    $rooms = array();
    if ($request->has('rooms_depa')) {
      $rooms_obj = json_decode(request('rooms_depa'));
      if ($rooms_obj) {
        foreach ($rooms_obj as $room) {
          $rooms[$room->room_id] = array(
            'clean_type' => 0,
            'extra' => $room->extra,
          );
        }
      }
    }
    if ($request->has('rooms_restant')) {
      $rooms_obj = json_decode(request('rooms_restant'));
      if ($rooms_obj) {
        foreach ($rooms_obj as $room) {
          $rooms[$room->room_id] = array(
            'clean_type' => 1,
            'extra' => $room->extra,
          );
        }
      }
    }
    try {
      $calendar->rooms()->sync($rooms);

      //CHECK THIS ----------------------<<<<<<<<<<<
      if (!$calendar->rooms()->exists()) {
        $calendar->delete();
      }
      //CHECK THIS ----------------------<<<<<<<<<<<

      return response()->json($calendar, 200);
    } catch (\Exception $e) {
      return response()->json(['message' => __('Cannot sync rooms'), 'errors' => ['exception' => $e->getMessage()]], 500);
    }

    return response()->json($calendar, 200);
  }
}
