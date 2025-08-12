<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Models\Employee;
use App\Models\Device;
use App\Models\Room;
use App\Models\Calendar;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ApiRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Get an Employee's single record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Employee $employee, Request $request)
    {
      
        $request->validate([
          'limit' => 'numeric',
        ]);

        $limit = $request->has('limit') ? $request->limit : 1;
        $record = Record::where('employee_id', $employee->id)
        ->select('employee_id', 'device_id', 'action', 'perform', 'identity', 'time')
        ->orderBy('time', 'DESC')
        ->limit( $limit )
        ->get();

        return response()->json( ['last_records' => $record], 200 );
    }

    /**
     * Store a single record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // return response()->json( $request->all(), 200 );

        $request->validate([
          'employee' => 'required|exists:employees,id',
          'device' => 'required|exists:devices,id',
          'action' => 'required|numeric',
          'perform' => 'required|numeric',
          'identity' => 'required|numeric',
          'time' => 'required|date',
          'depa' => 'array',
          'depa.*.room_id' => 'required|numeric',
          'depa.*.extra' => 'required|numeric',
          'depa.*.volunteer' => 'sometimes|required|exists:employees,id',
          'restant' => 'array',
          'restant.*.room_id' => 'required|numeric',
          'restant.*.extra' => 'required|numeric',
          'restant.*.volunteer' => 'sometimes|required|exists:employees,id',
        ]);

        

        $record = new Record();
        $record->employee_id = request('employee');
        $record->device_id = request('device');
        $record->action = request('action');
        $record->perform = request('perform');
        $record->identity = request('identity');
        $record->time = Carbon::parse(request('time'));
        $record->user_id = auth()->user()->is_device ? null : auth()->user()->id;
        // delete this test START
        $record->test_ipv4    = request()->ip();
        $record->test_user_id = auth()->user()->id;
        $record->test_type = 0;
        // delete this test END
        $record->save();

        try {

          if ( request('action') == 1 ) { //checkout
            $calendar = Calendar::where('date', Carbon::parse(request('time'))->format('Y-m-d'))->where('employee_id', request('employee'))->where('device_id', request('device'))->first();
            if ( !$calendar ) {
              return response()->json(['success' => "Record saved but no rooms added. Reason: No rooms assigned for the given date -> " . Carbon::parse(request('time'))->format('Y-m-d') ], 200);
            }

            $record->calendar_id = $calendar->id;
            $record->save();

            $rooms = new Collection();
            if ( $request->has('depa') ) {
                foreach (request('depa') as $room) {
                  $rooms->put( $room['room_id'], [
                    'clean_type' => 0,
                    'extra' => $room['extra'],
                    'status' => isset($room['status']) ? $room['status'] : 1, //revert to 0 when the API is updated
                    'volunteer' => isset($room['volunteer']) ? $room['volunteer'] : null,
                    'record_id' => $record->id
                  ]);
                }
            }
            if ( $request->has('restant') ) {
              $restant_obj = collect(request('restant'));
                foreach (request('restant') as $room) {
                  $rooms->put( $room['room_id'], [
                    'clean_type' => 1,
                    'extra' => $room['extra'],
                    'status' => isset($room['status']) ? $room['status'] : 1, //revert to 0 when the API is updated
                    'volunteer' => isset($room['volunteer']) ? $room['volunteer'] : null,
                    'record_id' => $record->id
                  ]);
                }
            }
            $record->calendar->rooms()->syncWithoutDetaching( $rooms );
          }
          return response()->json( ['success' => 'Record with ID ' . $record->id . ' created sucessfully'], 200 );
          
        } catch (\Exception $ex) {
          // return redirect()->back()->with([ 'error' => "Record cannot be created", 'message' => ['exception' => $ex->getMessage()] ]);
          return response()->json(['error' => "Record cannot be created", 'message' => ['exception' => $ex->getMessage()] ], 500);
        }

    }

    /**
     * Store multiple records.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulk_store(Request $request)
    {
        $request->validate([
          'records.*.employee' => 'required|exists:employees,id',
          'records.*.device' => 'required|exists:devices,id',
          'records.*.action' => 'required|numeric',
          'records.*.perform' => 'required|numeric',
          'records.*.identity' => 'required|numeric',
          'records.*.time' => 'required|date',
          'records.*.depa.*.room_id' => 'required|numeric',
          'records.*.depa.*.extra' => 'required|numeric',
          'records.*.depa.*.volunteer' => 'sometimes|required|exists:employees,id',
          'restant.*.restant.*.room_id' => 'required|numeric',
          'restant.*.restant.*.extra' => 'required|numeric',
          'records.*.restant.*.volunteer' => 'sometimes|required|exists:employees,id',
        ]);

        $counter = 0;

        try {
          foreach ( request('records') as $record_data) {
            $counter++;
  
            $record = new Record();
            $record->employee_id  = $record_data['employee'];
            $record->device_id    = $record_data['device'];
            $record->action       = $record_data['action'];
            $record->perform      = $record_data['perform'];
            $record->identity     = $record_data['identity'];
            $record->time         = Carbon::parse( $record_data['time'] );
            $record->user_id      = null;
            // delete this test START
            $record->test_ipv4    = request()->ip();
            $record->test_user_id = auth()->user()->id;
            $record->test_type = 1;
            // delete this test END
            $record->save();
  
            if ( $record_data['action'] == 1 ) { //checkin
              $calendar = Calendar::where('date', Carbon::parse( $record_data['time'] )->format('Y-m-d'))->where('employee_id', $record_data['employee'])->where('device_id', $record_data['device'])->first();
              if ( $calendar ) {
                // return response()->json( ['success' => $counter . ' Records created sucessfully'], 200 );
                // return response()->json(['error' => "No rooms assigned for the given date: " . Carbon::parse( $record_data['time'] )->format('Y-m-d') ], 200);
  
                $record->calendar_id = $calendar->id;
                $record->save();
    
                $rooms = new Collection();
                if ( array_key_exists( 'depa', $record_data ) ) {
                  foreach (  $record_data['depa'] as $room ) {
                    $rooms->put( $room['room_id'], [
                      'clean_type' => 0,
                      'extra' => $room['extra'],
                      'status' => isset($room['status']) ? $room['status'] : 1, //revert to 0 when the API is updated
                      'volunteer' => isset($room['volunteer']) ? $room['volunteer'] : null,
                      'record_id' => $record->id
                    ]);
                  }
                }
    
                if ( array_key_exists( 'restant', $record_data ) ) {
                  foreach (  $record_data['restant'] as $room ) {
                    $rooms->put( $room['room_id'], [
                      'clean_type' => 1,
                      'extra' => $room['extra'],
                      'status' => isset($room['status']) ? $room['status'] : 1, //revert to 0 when the API is updated
                      'volunteer' => isset($room['volunteer']) ? $room['volunteer'] : null,
                      'record_id' => $record->id
                    ]);
                  }
                }
                $record->calendar->rooms()->syncWithoutDetaching( $rooms );

              }
            }
  
          } //end foreach
          return response()->json( ['success' => $counter . ' Records created sucessfully'], 200 );
        } catch (\Exception $ex) {
          // return redirect()->back()->with([ 'error' => "Record cannot be created", 'message' => ['exception' => $ex->getMessage()] ]);
          return response()->json(['error' => "Record cannot be created", 'message' => ['exception' => $ex->getMessage()] ], 500);
        }

    }
}
