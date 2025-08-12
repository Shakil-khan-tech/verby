<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use App\Models\Employee;
use App\Models\Record;
use App\Models\Room;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Auth;
use Illuminate\Support\Collection;
use App\Mail\DeviceActivities;
use Illuminate\Support\Facades\Mail;
// use Session;
// use Redirect;
// use Debugbar;
use App\Jobs\DeviceActivitiesJob;

class DeviceController extends Controller
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
    public function index()
    {
        $this->authorize('viewAny', Device::class);

        $page_title = __('Devices');
        $page_description = __('All the devices');
        // $devices = Device::all();
        $devices = Device::available()->get();
        $total_employees = Employee::count();
        // $last_activities = Record::select('device_id as id')->selectRaw('MAX(created_at) last_action')->groupBy('device_id')->get();
        $last_activities = Device::select('devices.id')
        ->selectRaw('MAX(records.updated_at) updated_at')
        // ->selectRaw('IFNULL(MAX(records.updated_at),CURRENT_DATE()) updated_at')
        ->leftJoin('records','records.device_id','=','devices.id')
        ->groupBy('id')
        ->get();

        return view('pages.devices.index', compact('page_title', 'page_description', 'devices', 'total_employees', 'last_activities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Device::class);

        $page_title = __('Add a device');
        $page_description = __('Create new');

        return view('pages.devices.create', compact('page_title', 'page_description'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Device::class);

        // return request('rooms');
        $request->validate([
          'name' => 'required|unique:devices,name',
          'hotel_email' => 'sometimes|nullable|email',
          'hotel_technician_email' => 'sometimes|nullable|email',
          'rooms' => 'required',
        ]);


        // $no_of_devices = Device::count() + 1;
        $the_id = User::all()->last()->id + 1;

        $u = new User;
        $u->name = request('name');
        $u->email = 'device_' . $the_id . '@verby.ch';
        $u->password = bcrypt('password');
        $u->password_plain = request('password');
        $u->is_device = 1;
        $u->save();
        
        $d = new Device;
        $d->name = request('name');
        $d->hotel_email = request('hotel_email');
        $d->hotel_technician_email = request('hotel_technician_email');
        $d->user_id = $u->id;

        $rooms = [];
        if ( $request->has('rooms') ) {
          $rooms_req = json_decode(request('rooms'));

          if ( !empty($rooms_req) ) {
            foreach ($rooms_req as $key => $room) {
              $rooms[] = new Room([
                'name' => $room->name,
                'category' => $room->room_cat,
                'depa_minutes' => $room->depa_min,
                'restant_minutes' => $room->restant_min,
              ]);
            }
          }
        }

        try {
          $d->save();
          $d->rooms()->saveMany( $rooms );
          return redirect()->route('devices.index')->with([ 'success' => __('controllers.device.store.success', ['name' => $d->name]) ]);
        } catch (\Exception $ex) {
          return $ex;
          return redirect()->back()->with([ 'error' => __('Device cannot be created'), 'message' => ['exception' => $ex->getMessage()] ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function show(Device $device)
    {
        $this->authorize('manage', $device);

        $device = Device::with('rooms')->findOrFail($device->id);
        // return $device;
        $page_title = __('Device');
        $page_description = $device->name;
        $total_employees = Employee::count();
        $active_employees = $device->employees->where('end', '>', Carbon::now())->count();
        $last_activity = Record::where('device_id', '=', $device->id)
        ->selectRaw('MAX(updated_at) updated_at')
        ->get()
        ->first();

        // return $last_activity;

        return view('pages.devices.show', compact('page_title', 'page_description', 'device', 'total_employees', 'active_employees', 'last_activity'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function edit(device $device)
    {
        $this->authorize('manage', $device);

        // $device = Device::findOrFail($device->id);
        $device = Device::with('rooms')->findOrFail($device->id);
        $page_title = $device->name;
        $page_description = __('Edit the device');
        $total_employees = Employee::count();
        $last_activity = Record::where('device_id', '=', $device->id)
        ->selectRaw('MAX(updated_at) updated_at')
        ->get()
        ->first();

        // return $last_activity;

        return view('pages.devices.edit', compact('page_title', 'page_description', 'device', 'total_employees', 'last_activity'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, device $device)
    {
        $this->authorize('manage', $device);

        $request->validate([
          'name' => 'required',
          'hotel_email' => 'sometimes|nullable|email',
          'hotel_technician_email' => 'sometimes|nullable|email',
        ]);

        $d = Device::findOrFail($device->id);
        $d->name = request('name');
        $d->hotel_email = request('hotel_email');
        $d->hotel_technician_email = request('hotel_technician_email');
        $d->save();

        $rooms_add = [];
        $rooms_remove = [];
        $rooms_req = new Collection( json_decode(request('rooms')) );


        $roomsToRemove = $d->rooms->whereNotIn('name', $rooms_req->pluck('name'));
        $roomsToAdd = $rooms_req->whereNotIn('name', $d->rooms->pluck('name'));

        foreach ($roomsToRemove as $key => $room) {
          $rooms_remove[] = $room->id;
        }


        foreach ($roomsToAdd as $key => $room) {
          $rooms_add[] = new Room([
            'name' => $room->name,
            'category' => $room->room_cat,
            'depa_minutes' => $room->depa_min,
            'restant_minutes' => $room->restant_min,
          ]);
        }

        try {
          $d->rooms()->whereIn('id', $rooms_remove)->delete();
          $d->rooms()->saveMany( $rooms_add );
          return redirect()->back()->with([ 'success' => __('controllers.device.update.success', ['name' => $d->name]) ]);
        } catch (\Exception $ex) {
          return $ex;
          return redirect()->back()->with([ 'error' => __('Device cannot be updated'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function destroy(device $device)
    {
        $this->authorize('manage', $device);

        $d = Device::findOrFail( $device->id );
        try {
          $d->delete();
          return redirect()->route('devices.index')->with(['success' => __('controllers.device.delete.success', ['name' => $d->name]) ]);
        } catch (\Exception $e) {
          return redirect()->back()->with([ 'error' => __('Device cannot be deleted'), 'message' => ['exception' => $e->getMessage()] ]);
        }
    }

    /**
     * Display the auth of the device.
     *
     * @return \Illuminate\Http\Response
     */
    public function auth(Device $device)
    {
        $this->authorize('manage', $device);

        $page_title = $device->name;
        $page_description = __('Authentication');
        $item_active = 'auth';
        $can_change_pass = Auth::user()->hasRole(['super_admin', 'admin']) ? true : false;
        
        return view('pages.devices.auth', compact('page_title', 'page_description', 'item_active', 'device', 'can_change_pass'));
    }
    
    /**
     * Update the auth of the device.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\device  $device
     * @return \Illuminate\Http\Response
     */
    public function auth_update(Request $request, device $device)
    {
        $this->authorize('manage', $device);

        $request->validate([
          'password' => 'min:6|confirmed',
        ]);

        $device->user->password = bcrypt( request('password') );
        $device->user->password_plain = request('password');

        try {
          $device->user->save();
          return redirect()->back()->with([ 'success' => __('Device authentication updated sucessfully') ]);

        } catch (\Exception $ex) {
          return redirect()->back()->with([ 'error' => __('Device authentication cannot be updated'), 'message' => ['exception' => $ex->getMessage()] ]);
        }
    }

    /**
     * Display the auth of the device.
     *
     * @return \Illuminate\Http\Response
     */
    public function report(Device $device)
    {
        $this->authorize('manage', $device);

        $page_title = $device->name;
        $page_description = __('Report');
        $item_active = 'report';
        
        return view('pages.devices.report', compact('page_title', 'page_description', 'item_active', 'device'));
    }

    /**
     * A json report of device.
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarReport(Device $device, Request $request)
    {
        $this->authorize('manage', $device);

        if ( !$request->ajax() ) {
          // return response()->json(['message' => "Just ajax requests!" ], 500);
        }
        // defaults
        $page = 1;
        $perpage = 10;
        $order_by = 'updated_at';
        $sort = 'asc';
        $keyword = '';
        $from = Carbon::now()->startOfDay();
        $to = $from->copy()->endOfDay();

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

        if ( $request->has('query') && is_array(request('query')) ) {
          foreach( request('query') as $key => $value ) {
            if( $key == "generalSearch" ){
              $keyword = $value;
            } elseif( $key == "Date" ) {
              if ( !empty( $value['start'] || $value['end'] ) ) {
                $from = new Carbon( $value['start'] );
                $to = new Carbon( $value['end'] );
              }
            }
          }
        }

        $employees = Employee::select('id', 'name', 'surname', 'function')
        // whereHas('records') //use this maybe instead of following condition
        ->whereHas('records', function ($q) use($device, $from, $to) {
          $q->whereBetween('records.time', [$from, $to])
            ->where('device_id', $device->id);
        })
        ->with([
          'records' => function ($record) use ($device, $from, $to) {
            return $record->whereBetween('records.time', [$from, $to])
            ->where('device_id', $device->id)
            ->whereIn('action', [0,1]) //just checkin or checkout records, without pauses
            ->orderBy('time', 'ASC');
          }
        ])
        ->whereRaw( "CONCAT(name, ' ', surname) LIKE ?", ['%'.$keyword.'%'] )
        // ->get();
        ->paginate( $perpage, ['*'], null, $page );

        $total = 0;
        foreach ($employees as $employee) {

          $checkin = $checkout = $lastAction = null;
          $seconds = 0;
          // NOTE:
          //The following line is used if record starts with checkout,
          // meaning that the employee started before 1st of month with work and he is still working on the 1st of month
          // ex: Employee starts working on 2021-01-31 22:00:00 and finishes on 2021-02-01 06:00:00
          $checkin = $from;
          foreach ($employee->records as $record) {
            // NOTE: checkin action = 0 ; checkout action = 1
            if ($record->action == 0) {
              $lastAction = 'in';
              $checkin = $record->time;
              $checkout = null;
            } elseif ($record->action == 1) {
              $lastAction = 'out';
              $checkout = $record->time;
            }

            if ($lastAction == 'out' && $checkin && $checkout) {
              // $seconds += $checkin->diffInSeconds($checkout);
              $seconds += Carbon::parse($checkin)->diffInSeconds(Carbon::parse($checkout));
              $checkin = $checkout = null;
            }

            // TODO:
            // if employee has more then one checkout in a row (which is a bug on the device actually),
            // use the last checkout time.
            // this can be achieved as this:
            // if(currentAction == '1' AND previousAction == '1') {
            //  $seconds -= $checkin->diffInSeconds($previousActionTime);
            //  $seconds += $checkin->diffInSeconds($currentActionTime); }
          }
          // $employee->forget('records');
          $total += $seconds;
          $employee->work_time = CarbonInterval::seconds($seconds)->cascade()->forHumans(['short' => true, 'options' => 0]);
          $employee->work_time_decimal = $seconds / 3600;
        }

        $meta = [
          "page" => $employees->currentPage(),
          "pages" => intval(count($employees) / $perpage),
          "perpage" => $perpage,
          "total" => $employees->total(),
          "sort" => $sort,
          "field" => $order_by,
          // 'total_hours' => CarbonInterval::seconds($total)->cascade()->forHumans(['short' => true, 'options' => 0]),
          'total_hours' => number_format( (float)($total / 3600), 2, '.', '' ) . ' ha',
          'date_from' => $from->format('Y-m-d'),
          'date_to' => $to->format('Y-m-d'),
        ];

        return response()->json( ['meta' => $meta, 'data' => $employees->items()], 200 );
    }

    public function check_activity()
    {

      DeviceActivitiesJob::dispatchSync();
      return;

    }
}
