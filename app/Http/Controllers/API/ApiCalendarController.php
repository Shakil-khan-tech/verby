<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Calendar;
use App\Models\Employee;
use App\Models\Device;
use Illuminate\Http\Request;
use DB;
use Carbon\CarbonPeriod;

class ApiCalendarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->authorizeResource('user');
    }


    /**
     * Get all employees in json format.
     *
     * @return json
     */
    public function get(Device $device, Request $request)
    {
        $request->validate([
          'employee' => 'required|exists:employees,id',
          'date' => 'required|date_format:Y-m-d',
        ]);
        $calendar = Calendar::where('device_id', $device->id)
        ->where('employee_id', request('employee'))
        ->where('date', request('date'))
        ->has('rooms')
        ->with(['rooms' => function ($q) {
          //slow
          // $q->where('status', 0)
          // ->orWhereNull('record_id');
          $q->where(function($subquery) {
            $subquery->where('status',  0)
            ->orWhereNull('record_id');
          });
        }])
        ->first();


        if ( !$calendar ) {
            return response()->json( ['depa' => collect(), 'restant' => collect()], 200 );
        }

        $depas = collect();
        $restants = collect();

        foreach ($calendar->rooms as $room) {
          $new_record = collect([
            'id' => $room->id,
            'name' => $room->name,
            'category' => $room->category,
            'extra' => $room->pivot->extra,
            'status' => $room->pivot->status,
          ]);

          if ( $room->pivot->clean_type == 0 ) {
            $depas->push( $new_record );
          } elseif ( $room->pivot->clean_type == 1 ) {
            $restants->push( $new_record );
          }
        }

        return response()->json( ['depa' => $depas, 'restant' => $restants], 200 );
    }
}
