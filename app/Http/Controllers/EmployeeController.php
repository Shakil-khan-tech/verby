<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Device;
use App\Models\Record;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Schema;
use Auth;
use Debugbar;

use App\Http\Traits\RecordTrait;
use App\Models\Country;
use App\Models\EmployeeContract;
use Illuminate\Support\Facades\Auth as FacadesAuth;

class EmployeeController extends Controller
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
    $this->authorize('viewAny', EmployeeContract::class);
    $devices = Device::available()->get()->toArray();
    $page_title = __('Employees');
    $page_description = __('All the employees');
    if (!Auth::user()->hasRole(['super_admin', 'admin'])) {
      $page_description .= ' ' . __('in devices: ') . Device::available()->pluck('name')->join(', ');
    }
    return view('pages.employees.index', compact('page_title', 'page_description', 'devices'));
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {

    $this->authorize('create', Employee::class);
    $page_title = __('Employee');
    $page_description = __('Create employee');
    $devices = Device::all();
    $countries = Country::orderByRaw('code = "CH" DESC')  // CH first
      ->get();

    return view('pages.employees.create', compact('page_title', 'page_description', 'devices', 'countries'));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $this->authorize('create', Employee::class);

    $request->validate([
      'name' => 'required',
      'email' => 'required',
      // 'phone' => 'required',
      'function' => 'required',
      'PartTime' => 'required',
      'locations' => 'sometimes|required|array',
      // 'EhChf' => 'required',
      //'decki250' => 'required',
      //'decki200' => 'required',
      'BVG' => 'required',
      'start' => 'required',
      // 'end' => 'required',
      'pin' => 'required',
      //'additional_income' => 'required',
      'married_since' => 'required',
      'religion' => 'required',
      'children' => 'required',
      'child_allowance' => 'required',
    ]);
    if ($request->input('additional_income_toggle') === 'yes') {
      $request->validate([
        'additional_income' => 'required|numeric',
      ]);
    }
    if ($request->input('child_allowance') === 'yes') {
      $request->validate([
        'decki200' => 'required|numeric',
        'decki250' => 'required|numeric',
      ]);
    }
    $u = new Employee;
    $u->name = request('name');
    $u->surname = request('surname');
    $u->phone = request('phone');
    $u->email = request('email');
    $u->DOB = request('DOB');
    $u->gender = request('gender');
    $u->maried = request('maried');
    $u->strasse = request('strasse');
    $u->PLZ = request('PLZ');
    $u->ORT1 = request('ORT1');
    $u->ORT = request('ORT');
    $u->TAX = request('TAX');
    $u->AHV = request('AHV');
    $u->bankname = request('bankname');
    $u->IBAN = request('IBAN');
    $u->pin = request('pin');
    $u->card = request('card');
    $u->sage_number = request('sage_number');
    $u->api_monitoring = request('api_monitoring') ? 1 : 0;
    $u->function = request('function');
    $u->PartTime = request('PartTime');
    $u->noqnaSmena = request('noqnaSmena') == 'on' ? 1 : 0;
    $u->EhChf = request('EhChf') ? request('EhChf') : 0;
    $u->rroga = request('rroga');
    $u->decki250 = request('decki250');
    $u->decki200 = request('decki200');
    $u->BVG = request('BVG');
    $u->Perqind1 = request('Perqind1');
    $u->Perqind2 = request('Perqind2');
    $u->Perqind3 = request('Perqind3');
    $u->start = request('start');
    $u->end = request('end');
    $u->oldSaldoF = request('oldSaldoF');
    $u->oldSaldo13 = request('oldSaldo13');
    $u->work_percetage = request('work_percetage');
    $u->country_id = request('nationality');
    $u->additional_income = request('additional_income');
    $u->married_since = request('married_since');
    $u->religion = request('religion');
    $u->children = request('children');
    $u->child_allowance = request('child_allowance');
    $u->work_permit = request('work_permit');
    $u->work_permit_expiry = request('work_permit_expiry');
    $u->employee_type = 1;

    try {
      $u->save();
      $u->devices()->sync(request('locations'));
      return redirect()->back()->with(['success' => __('Employee created'), 'id' => $u->id]);
    } catch (\Exception $ex) {
      return redirect()->back()->with(['error' => __('Employee cannot be created'), 'message' => ['exception' => $ex->getMessage()]])->withInput();
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function show(Employee $employee)
  {
    $this->authorize('manage', $employee);
    // $employees = Employee::all()->take(10);
    $page_title = $employee->fullname;
    $page_description = __('Employee');
    $item_active = 'personal';
    $pushimi = DB::select(
      '
          SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
          FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $employee->id .
        ' ORDER BY fillimi DESC'
    );
    $countries = Country::orderByRaw('code = "CH" DESC')  // CH first
      ->get();
    return view('pages.employees.show', compact('page_title', 'page_description', 'employee', 'item_active', 'pushimi', 'countries'));
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function overview(Employee $employee)
  {
    $this->authorize('manage', $employee);

    $page_title = $employee->fullname;
    $page_description = __('Employee');
    $item_active = 'overview';
    $devices = Device::all();
    $pushimi = DB::select(
      '
          SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
          FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $employee->id .
        ' ORDER BY fillimi DESC'
    );

    return view('pages.employees.overview', compact('page_title', 'page_description', 'employee', 'item_active', 'pushimi', 'devices'));
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function deduction(Employee $employee)
  {
    $this->authorize('manage', $employee);

    $page_title = $employee->fullname;
    $page_description = __('Employee');
    $item_active = 'deduction';
    $pushimi = DB::select(
      '
          SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
          FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $employee->id .
        ' ORDER BY fillimi DESC'
    );

    return view('pages.employees.deduction', compact('page_title', 'page_description', 'employee', 'item_active', 'pushimi'));
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function insurance(Employee $employee)
  {
    $this->authorize('manage', $employee);

    $page_title = $employee->fullname;
    $page_description = __('Employee');
    $item_active = 'insurance';
    $pushimi = DB::select(
      '
          SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
          FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $employee->id .
        ' ORDER BY fillimi DESC'
    );

    return view('pages.employees.insurance', compact('page_title', 'page_description', 'employee', 'item_active', 'pushimi'));
  }

  /**
   * Display the specified resource.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function vacation(Employee $employee, Request $request)
  {
    $this->authorize('manage', $employee);

    $request->validate([
      'action' => 'required'
    ]);

    if (request('action') == 'add') {
      $e = Employee::findOrFail($employee->id);
      $start = request('date_start');
      $end = request('date_end');

      $period = CarbonPeriod::create($start, $end);
      $sql = 'INSERT INTO pushimi (employee_id, data, fillimi, mbarimi) VALUES ';
      $values = [];
      foreach ($period as $date) {
        $values[] = sprintf('(%s, "%s", "%s", "%s")', $e->id, $date->format('Y-m-d'), $start, $end);
      }
      $sql = $sql . implode(', ', $values);
      // echo $sql;

      try {
        DB::statement($sql);

        $pushimi = DB::select(
          'SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
              FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $e->id .
            ' ORDER BY fillimi DESC'
        );
        return response()->json(['success' => __('Vacation added successfully'), 'pushimi' => $pushimi], 200);
      } catch (\Exception $ex) {
        return response()->json(['message' => __('Vacation cannot be added'), 'errors' => ['exception' => $ex->getMessage()]], 500);
      }
    }

    if (request('action') == 'delete') {
      $e = Employee::findOrFail($employee->id);
      $sql = 'DELETE FROM pushimi WHERE employee_id=' . $e->id . ' and fillimi="' . request('date_start') . '" and mbarimi="' . request('date_end') . '"';

      try {
        DB::statement($sql);

        $pushimi = DB::select(
          'SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
              FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $e->id .
            ' ORDER BY fillimi DESC'
        );
        return response()->json(['success' => __('Vacation deleted successfully'), 'pushimi' => $pushimi], 200);
      } catch (\Exception $ex) {
        return response()->json(['message' => __('Vacation cannot be deleted'), 'errors' => ['exception' => $ex->getMessage()]], 500);
      }
    }

    return response()->json(['message' => __('General error'), 'errors' => ['exception' => 'no action']], 500);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function edit(Employee $employee)
  {
    $this->authorize('manage', $employee);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Employee $employee)
  {
    $this->authorize('manage', $employee);
    if ($request->has('show')) {
      $request->validate([
        'name' => 'required',
        'gender' => 'required',
        'maried' => 'required',
        'ORT' => 'required',
        'pin' => 'required',
        // 'additional_income' => 'required',
        'married_since' => 'required',
        'religion' => 'required',
        'children' => 'required',
        //'child_allowance' => 'required',
        'work_permit' => 'required',
        'work_permit_expiry' => 'required',
      ]);

      if ($request->input('additional_income_toggle') === 'yes') {
        $request->validate([
          'additional_income' => 'required|numeric',
        ]);
      }

      $u = Employee::findOrFail($employee->id);
      $u->name = request('name');
      $u->surname = request('surname');
      $u->phone = request('phone');
      $u->email = request('email');
      $u->DOB = request('DOB');
      $u->gender = request('gender');
      $u->maried = request('maried');
      $u->strasse = request('strasse');
      $u->PLZ = request('PLZ');
      $u->ORT1 = request('ORT1');
      $u->ORT = request('ORT');
      $u->TAX = request('TAX');
      $u->AHV = request('AHV');
      $u->bankname = request('bankname');
      $u->IBAN = request('IBAN');
      $u->pin = request('pin');
      $u->card = request('card');
      $u->country_id = request('nationality');
      $u->additional_income = request('additional_income');
      $u->married_since = request('married_since');
      $u->religion = request('religion');
      $u->children = request('children');
      $u->work_permit = request('work_permit');
      $u->work_permit_expiry = request('work_permit_expiry');

      try {
        $u->save();
        return redirect()->back()->with(['success' => __('Employee updated'), 'id' => $u->id]);
      } catch (\Exception $ex) {
        return redirect()->back()->with(['error' => __('Employee cannot be updated'), 'message' => ['exception' => $ex->getMessage()]]);
      }
    }
    if ($request->has('overview')) {
      $request->validate([
        'sage_number' => 'required',
        // 'function' => 'required',
        // 'PartTime' => 'required',
        'locations' => 'required|array|min:1',
        'start' => 'required',
        'end' => 'nullable|after_or_equal:start',
      ]);

      $u = Employee::findOrFail($employee->id);
      $u->sage_number = request('sage_number');
      $u->api_monitoring = request('api_monitoring') ? 1 : 0;
      $u->function = request('function');
      $u->PartTime = request('PartTime');
      $u->noqnaSmena = request('noqnaSmena') == 'on' ? 1 : 0;

      // if start & end dates are changed, add new entry
      if (request('start') != $u->start || request('end') != $u->end) {
        if ($u->start && $u->end) {
          $u->entries()->updateOrCreate(
            ['start' => $u->start, 'end' => $u->end],
            ['start' => $u->start, 'end' => $u->end]
          );
        }
      }

      $u->start = request('start');
      $u->end = request('end');

      // entry dates DISCONTINUED
      // if ( request('start') != $u->start || request('end') != $u->end ) {
      //   $current_period = CarbonPeriod::create($u->start, $u->end);
      //   // check if dates overlap with start and end columns
      //   if ( $current_period->overlaps( CarbonPeriod::create(request('start'), request('end')) ) ) {
      //     return redirect()->back()->with(['error' => __('Employee cannot be updated'), 'message' => ['exception' => 'Dates overlap']]);
      //   }

      //   // check if dates overlap with entries table
      //   foreach ($u->entries as $entry) {
      //     $entry_period = CarbonPeriod::create($entry->start, $entry->end);
      //     if ( $entry_period->overlaps( CarbonPeriod::create(request('start'), request('end')) ) ) {
      //       return redirect()->back()->with(['error' => __('Employee cannot be updated'), 'message' => ['exception' => 'Dates overlap in history']]);
      //     }
      //   }

      //   $u->entries()->create([
      //     'start' => $u->start,
      //     'end' => $u->end
      //   ]);

      //   $u->start = request('start');
      //   $u->end = request('end');
      // }

      try {
        $u->save();
        $u->devices()->sync(request('locations'));
        return redirect()->back()->with(['success' => __('Employee updated'), 'id' => $u->id]);
      } catch (\Exception $ex) {
        return redirect()->back()->with(['error' => __('Employee cannot be updated'), 'message' => ['exception' => $ex->getMessage()]]);
      }
    }
    if ($request->has('deduction')) {
      // $request->validate([
      //   'decki200' => 'required',                   
      //   'BVG' => 'required',
      //   'Perqind1' => 'required',
      //   'Perqind2' => 'required',
      //   'Perqind3' => 'required',
      // ]);

      $request->validate([
        //'decki200' => 'required',
        'BVG' => 'required',
        'Perqind1' => 'required',
        'Perqind2' => 'required',
        'Perqind3' => 'required',
      ], [
        //'decki200.required' => 'Child allowance 200 field is required.',
        'BVG.required' => 'BVG field is required.',
        'Perqind1.required' => 'Holiday Compensation 1 is required.',
        'Perqind2.required' => 'Holiday Compensation 2 is required.',
        'Perqind3.required' => '13th monthly salary is required.',
      ]);

      if ($request->input('child_allowance') === 'yes') {
        $request->validate(
          [
            'decki200' => 'required|numeric',
            'decki250' => 'required|numeric',
          ],
          [
            'decki200.required' => 'Child allowance 200 field is required.',
            'decki200.numeric'  => 'Child allowance 200 must be a number.',
            'decki250.required' => 'Education allowance 250.',
            'decki250.numeric'  => 'Education allowance 250 must be a number.',
          ]
        );
      }
      $u = Employee::findOrFail($employee->id);
      $u->EhChf = request('EhChf') ? request('EhChf') : 0;
      $u->rroga = request('rroga') ? request('rroga') : 0;
      $u->child_allowance = request('child_allowance');
      $u->decki250 = request('decki250');
      $u->decki200 = request('decki200');
      $u->BVG = request('BVG');
      $u->Perqind1 = request('Perqind1');
      $u->Perqind2 = request('Perqind2');
      $u->Perqind3 = request('Perqind3');
      $u->oldSaldoF = request('oldSaldoF');
      $u->oldSaldo13 = request('oldSaldo13');
      $u->work_percetage = request('work_percetage');

      try {
        $u->save();
        return redirect()->back()->with(['success' => __('Employee updated'), 'id' => $u->id]);
      } catch (\Exception $ex) {
        return redirect()->back()->with(['error' => __('Employee cannot be updated'), 'message' => ['exception' => $ex->getMessage()]]);
      }
    }
    if ($request->has('insurance')) {
      $u = Employee::findOrFail($employee->id);

      if ($request->has('insurance_6')) {
        if (request('insurance_6') == 'yes') {
          $u->insurance_6_1 = 1;
          $u->insurance_6_5 = 0;
        } else {
          $u->insurance_6_1 = 0;
          $u->insurance_6_5 = 1;
        }
      }

      $u->insurance_6_2 = request('insurance_6_2');
      $u->insurance_6_3 = request('insurance_6_3');
      $u->insurance_6_4 = request('insurance_6_4');
      $u->insurance_7_1 = request('insurance_7_1');
      $u->insurance_15_1 = request('insurance_15_1') ? 1 : 0;
      $u->insurance_15_2 = request('insurance_15_2') ? 1 : 0;
      $u->insurance_15_3 = request('insurance_15_3');
      $u->insurance_15_4 = request('insurance_15_4') ? 1 : 0;
      $u->insurance_15_5 = request('insurance_15_5');
      $u->insurance_15_6 = request('insurance_15_6');
      $u->insurance_15_7 = request('insurance_15_7');
      $u->insurance_16_1 = request('insurance_16_1');

      try {
        $u->save();
        return redirect()->back()->with(['success' => __('Employee updated'), 'id' => $u->id]);
      } catch (\Exception $ex) {
        return redirect()->back()->with(['error' => __('Employee cannot be updated'), 'message' => ['exception' => $ex->getMessage()]]);
      }
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function destroy(Employee $employee, Request $request)
  {
    $this->authorize('manage', $employee);

    // delete
    $e = Employee::findOrFail($employee->id);
    $e->delete();

    // redirect
    if ($request->ajax()) {
      return response()->json(['success' => __('Employee deleted sucessfully')], 200);
    } else {
      // Session::flash('message', 'Employee deleted sucessfully');
      // return Redirect::to('employee.index');
      return redirect()->route('employees.index')->with(['success' => __('Employee deleted sucessfully')]);
    }
  }

  /**
   * Get all employees in json format.
   *
   * @return json
   */
  public function getall(Request $request)
  {
    $this->authorize('viewAny', Employee::class);

    //get devices that the user has access to
    $devices_arr = Device::available()->pluck('id')->toArray();

    if ($request->has('query')) {

      $search = $request->get('query');
      if (!is_array($search)) {

        $employees = Employee::inDevices($devices_arr)->orderBy('id', 'DESC')->get();
      } else {

        $employees = Employee::inDevices($devices_arr)
          ->where(function ($q) use ($search) {
            foreach ($search as $key => $value) {
              if ($key == "generalSearch") {
                $q->where(function ($qr) use ($value) {
                  $qr->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ['%' . $value . '%'])->orWhere('id', 'like', ['%' . $value . '%']);
                });
              } elseif ($key == 'active_status') {

                $q->where(function ($qr) use ($value) {
                  if ($value == 2) {
                    $qr->whereHas('entries');
                  } elseif ($value == 1) {
                    $qr->active();
                  } else {
                    $qr->inactive();
                  }
                });
              } elseif ($key == 'device') {
                $q->where(function ($qr) use ($value) {
                  $qr->whereHas('devices', function ($query) use ($value) {
                    $query->whereIn('id', [$value])->select('id', 'name');
                  });
                });

                // ->with('devices:id,name');
                // $q->where( 'employee.device' == $key );
              } elseif ($value != null) {
                $q->where(function ($qr) use ($key, $value) {
                  // $qr->whereRaw( "CONCAT(name, ' ', surname) LIKE ?", ['%'.$value.'%'] )->orWhere('id', 'like', ['%'.$value.'%']);
                  $qr->where($key, 'like', "%{$value}%");
                });
              }
            }
          })->get();
      }
    } else {
      $employees = Employee::inDevices($devices_arr)->orderBy('id', 'DESC')->get();
    }

    // return $employees->count();

    $meta = [
      "page" => 1,
      "pages" => 1,
      "perpage" => -1,
      "total" => count($employees),
      "sort" => "desc",
      "field" => "RecordID",
      // "kerkimi" => request('query')['generalSearch']
    ];

    // return response()->json( ['meta' => $meta, 'data' => $employees], 200 );
    return response()->json($employees, 200);
  }

  /**
   * Employees files attached.
   *
   * @return json
   */
  public function files(Employee $employee)
  {
    $this->authorize('manage', $employee);

    $page_title = $employee->fullname;
    $page_description = __('Employee');
    $item_active = 'files';
    $pushimi = DB::select(
      '
          SELECT DISTINCT DATE_FORMAT(pushimi.fillimi, "%Y-%m-%d") as fillimi, DATE_FORMAT(pushimi.mbarimi, "%Y-%m-%d") as mbarimi, DATEDIFF(pushimi.mbarimi, pushimi.fillimi) as days
          FROM pushimi inner JOIN employees ON pushimi.employee_id = employees.id AND pushimi.employee_id = ' . $employee->id .
        ' ORDER BY fillimi DESC'
    );

    return view('pages.employees.files', compact('page_title', 'page_description', 'employee', 'item_active', 'pushimi'));
  }

  /**
   * Get all employees in json format.
   *
   * @return json
   */
  public function get_files(Employee $employee, Request $request)
  {
    $page = 1;
    $perpage = 10;
    $order_by = 'created_at';
    $sort = 'asc';
    $search = null;

    if ($request->has('pagination')) {
      if (!empty($request->get('pagination')['page'])) {
        $page = $request->get('pagination')['page'];
      }
      if (!empty($request->get('pagination')['perpage'])) {
        $perpage = $request->get('pagination')['perpage'];
      }
    }

    if ($request->has('sort')) {
      if (is_array(request('sort'))) {
        $sort = request('sort')['sort'];
      }
    }

    if ($request->has('query')) {
      if (is_array($request->get('query'))) {
        $search = $request->get('query');
      }
    }

    $files = $employee
      ->media()->where('collection_name', 'files')
      ->where(function ($q) use ($search) {
        if (!$search) return;

        foreach ($search as $key => $value) {
          if ($key == "generalSearch") {
            $q->whereRaw("name LIKE ?", ['%' . $value . '%']);
          }

          if ($key == "Date") {
            if (!empty($value['start'] || $value['end'])) {
              $start = new Carbon($value['start']);
              $start = $start->startOfDay();
              $end = new Carbon($value['end']);
              $end = $end->endOfDay();
              $q->whereBetween('updated_at', [$start, $end]);
            }
          }
        }
      })
      ->orderBy($order_by, $sort)
      ->paginate($perpage, ['*'], null, $page);

    $meta = [
      "page" => $files->currentPage(),
      "pages" => intval(count($files) / $perpage),
      "perpage" => $perpage,
      "total" => $files->total(),
      "sort" => $sort,
      "field" => $order_by,
    ];

    return response()->json(['meta' => $meta, 'data' => $files->items()], 200);

    // $emp = Employee::find($employee->id)->with('media')->get();
    // return $emp;

    return $employee->media;
    return $employee->getMedia('files');
  }

  public function store_files(Employee $employee, Request $request)
  {

    $fileAdders = $employee->addMultipleMediaFromRequest(['files'])
      ->each(function ($fileAdder) {
        $fileAdder->toMediaCollection('files');
      });
  }

  public function download_files(Media $media, Request $request)
  {
    return $media->toResponse($request);
  }
  public function preview_files(Media $media, Request $request)
  {
    $mediaData = $media->toArray();
    return response()->json([
      'media' => asset('storage/' . $mediaData['id'] . '/' . $mediaData['file_name']),
      'file_name' => $mediaData['file_name'],

    ]);
  }

  public function delete_files(Media $media, Request $request)
  {
    $media->delete();
    return response()->json(['success' => __('Files deleted successfully')], 200);
  }

  /**
   * Delete Entry Date for an employee.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function delete_entry_date(Employee $employee, Request $request)
  {
    $this->authorize('manage', $employee);

    $request->validate([
      'start' => 'required|date',
      'end' => 'required|date',
      'entry_id' => 'required|exists:entries,id',
    ]);

    // check if data exists in tables
    $plans = $employee->plans()->whereBetween('dita', [request('start'), request('end')])->get();
    $calendars = $employee->calendars()->whereBetween('date', [request('start'), request('end')])->get();
    $records = $employee->records()->whereBetween('time', [request('start'), request('end')])->get();

    if (count($plans) > 0 || count($calendars) > 0 || count($records) > 0) {
      return response()->json(['title' => __('Entry date cannot be deleted'), 'message' => __('Data exists in tables for current employee')], 200);
    } else {
      $employee->entries->where('id', request('entry_id'))->first()->delete();
    }

    return response()->json(['success' => true, 'title' => __('Success'), 'message' => __('Entry date deleted successfully')], 200);
  }

  /**
   * Display the view of deleted employees.
   *
   * @return \Illuminate\Http\Response
   */
  public function deleted()
  {
    $this->authorize('manage_employees', Employee::class);

    $page_title = __('Deleted Employees');
    $page_description = __('All deleted employees');
    if (!Auth::user()->hasRole(['super_admin', 'admin'])) {
      $page_description .= ' ' . __('in devices: ') . Device::available()->pluck('name')->join(', ');
    }

    return view('pages.employees.deleted', compact('page_title', 'page_description'));
  }

  /**
   * Restore the deleted employee.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function restore(Request $request)
  {
    $this->authorize('manage_employees', Employee::class);

    // restore
    $e = Employee::withTrashed()->find(request()->employee_id);
    $e->restore();

    // redirect
    if ($request->ajax()) {
      return response()->json(['success' => __('Employee restored sucessfully')], 200);
    } else {
      return redirect()->route('employees.index')->with(['success' => __('Employee restored sucessfully')]);
    }
  }

  /**
   * Get all deleted employees in json format.
   *
   * @return json
   */
  public function getall_deleted(Request $request)
  {
    $this->authorize('viewAny', Employee::class);

    //get devices that the user has access to
    $devices_arr = Device::available()->pluck('id')->toArray();

    if ($request->has('query')) {

      $search = $request->get('query');
      if (!is_array($search)) {
        $employees = Employee::inDevices(($devices_arr))->orderBy('id', 'DESC')->onlyTrashed()->get();
      } else {
        $employees = Employee::onlyTrashed()->where(function ($q) use ($search) {
          foreach ($search as $key => $value) {
            if ($key == "generalSearch") {
              $q->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ['%' . $value . '%'])
                ->orWhere('id', 'like', ['%' . $value . '%']);
            } elseif ($key == 'active_status') {
              if ($value == 1) {
                $q->active();
              } else {
                $q->inactive();
              }
            } elseif ($key == 'device') {
              $q->whereHas('devices', function ($query) use ($value) {
                $query->whereIn('id', [$value]);
              })
                ->with('devices:id,name');
            } elseif ($key == 'gender') {
              $q->where('gender', '=', $value);
            } elseif ($value != null) {
              $q->where($key, 'like', "%{$value}%");
            }
          }
        })->get();
      }
    } else {
      $employees = Employee::onlyTrashed()->orderBy('id', 'DESC')->get();
    }

    $meta = [
      "page" => 1,
      "pages" => 1,
      "perpage" => -1,
      "total" => count($employees),
      "sort" => "desc",
      "field" => "RecordID",
      // "kerkimi" => request('query')['generalSearch']
    ];

    // return response()->json( ['meta' => $meta, 'data' => $employees], 200 );
    return response()->json($employees, 200);
  }

  /**
   * Display the statistics page.
   *
   * @param  \App\Models\Employee  $employee
   * @return \Illuminate\Http\Response
   */
  public function statistics()
  {
    $this->authorize('viewAny', Employee::class);

    $page_title = __('Statistics');
    $page_description = __('Employee statistics');

    return view('pages.employees.statistics', compact('page_title', 'page_description'));
  }

  /**
   * Get statistics in json format.
   *
   * @return json
   */
  public function statistics_ajax(Request $request)
  {
    $this->authorize('viewAny', Employee::class);

    // if ( $request->has('query') && isset( $request->get('query')['month'] ) ) {
    //   try {
    //       $from = Carbon::parse( request('query')['month'] )->firstOfMonth()->startOfDay();
    //       $to = $from->copy()->lastOfMonth()->endOfDay();
    //   } catch (\Exception $e) {
    //       // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
    //   }
    // } else {
    //   $from = Carbon::now()->firstOfMonth()->startOfDay();
    //   $to = $from->copy()->lastOfMonth()->endOfDay();
    // }

    $from = Carbon::now()->firstOfMonth()->startOfMonth();
    $to = $from->copy()->lastOfMonth()->endOfMonth();

    if ($request->has('query')) {
      if (
        isset($request->get('query')['Date']) &&
        isset($request->get('query')['Date']['start']) &&
        isset($request->get('query')['Date']['end'])
      ) {
        try {
          $from = Carbon::parse(request('query')['Date']['start'])->startOfMonth();
          $to = Carbon::parse(request('query')['Date']['end'])->endOfMonth();
        } catch (\Exception $e) {
          // return response()->json(['message' => "Cannot load report", 'errors' => ['exception' => $e->getMessage()] ], 500);
        }
      }
    }

    $months = $to->diffInMonths($from) + 1;

    //get devices that the user has access to
    $devices_arr = Device::available()->pluck('id')->toArray();

    $page = 1;
    $perpage = 10;
    $order_by = 'created_at';
    $sort = 'asc';
    $search = null;

    if ($request->has('pagination')) {
      if (!empty($request->get('pagination')['page'])) {
        $page = $request->get('pagination')['page'];
      }
      if (!empty($request->get('pagination')['perpage'])) {
        $perpage = $request->get('pagination')['perpage'];
      }
    }

    if ($request->has('sort')) {
      if (is_array(request('sort'))) {
        $sort = request('sort')['sort'];
      }
    }

    if ($request->has('query')) {
      if (is_array($request->get('query'))) {
        $search = $request->get('query');
      }
    }

    $employees = Employee::inDevices($devices_arr)
      ->where(function ($q) use ($search) {
        if (!$search) return;

        foreach ($search as $key => $value) {

          if ($key == "generalSearch") {

            $q->where(function ($qr) use ($value) {
              $qr->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ['%' . $value . '%'])->orWhere('id', 'like', ['%' . $value . '%']);
            });
          } elseif ($key == 'active_status') {

            $q->where(function ($qr) use ($value) {
              if ($value == 1)
                $qr->active();
              else
                $qr->inactive();
            });
          } elseif ($key == 'device') {

            $q->where(function ($qr) use ($value) {
              $qr->whereHas('devices', function ($query) use ($value) {
                $query->whereIn('id', [$value])->select('id', 'name');
              });
            });
          } elseif ($value != null && in_array($key, Schema::getColumnListing('employees'))) {

            $q->where(function ($qr) use ($key, $value) {
              $qr->where($key, 'like', "%{$value}%");
            });
          }
        }
      })
      ->orderBy($order_by, $sort);
    $allIds = $employees->pluck('id');
    // ->paginate( $perpage, ['*'], null, $page );
    $employees = $employees->paginate($perpage, ['*'], null, $page);
    $employees->map(function ($employee) {
      $employee->seconds = 0;
      return $employee;
    });



    $employee_records = Record::whereIn('employee_id', $employees->pluck('id'))
      ->where(function ($q) use ($from, $to) {
        $q->whereBetween('time', [$from, $to]);
      })
      ->select('id', 'action', 'time', 'perform', 'employee_id')
      ->orderBy('time', 'ASC')
      ->get()
      ->groupBy('employee_id');

    Debugbar::info($employees->count());
    Debugbar::info($employee_records->count());

    $period = CarbonPeriod::create($from, '1 day', $to);
    /*matrix*/
    foreach ($employee_records as $employee_id => $employee_records) {
      $matrix = $this->month_employee_matrix($period, $employee_records);
      $seconds = 0;
      foreach ($matrix as $key => $day) {
        $seconds += $this->calculate_hours($day);
      }

      $employees->map(function ($employee) use ($employee_id, $matrix, $seconds) {
        if ($employee->id == $employee_id) {
          $employee->seconds = $seconds;
        }
        return $employee;
      });
    }


    $meta = [
      "page" => $employees->currentPage(),
      "pages" => intval(count($employees) / $perpage),
      "perpage" => $perpage,
      "total" => $employees->total(),
      "sort" => $sort,
      "field" => $order_by,
      "rowIds" => $allIds,
      // "field" => "id",
    ];

    return response()->json(['meta' => $meta, 'data' => $employees->items(), 'months' => $months, 'timeout' => true, 'r' => $employee_records], 200);
  }


  public function updateMediaName(Request $request, Media $media)
  {
    try {
      // Validate input
      $validated = $request->validate([
        'name' => 'required|string|max:255',
      ]);

      // Skip update if name didn't change
      if ($media->name === $validated['name']) {
        return response()->json(['message' => __('No changes detected.')], 200);
      }

      // Update and save
      $media->name = $validated['name'];
      $media->save();

      return response()->json([
        'success' => __('File name updated successfully.'),
        'name' => $media->name,
      ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
      return response()->json([
        'message' => __('Validation failed.'),
        'errors' => $e->errors()
      ], 422);
    } catch (\Exception $e) {
      Log::error('Media name update failed', [
        'media_id' => $media->id,
        'error' => $e->getMessage()
      ]);

      return response()->json([
        'message' => __('Something went wrong.'),
        'errors' => [$e->getMessage()]
      ], 500);
    }
  }
}
