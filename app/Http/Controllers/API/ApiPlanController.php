<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Employee;
use App\Models\Device;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class ApiPlanController extends Controller
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
        
        $calendar = Plan::select('symbol')
        ->where('device_id', $device->id)
        ->where('employee_id', request('employee'))
        ->where('dita', request('date'))
        ->first();

        if ( !$calendar ) {
          return response()->json( [], 200 );
        }

        //1
        /*
        $symbols_arr = explode( '-', $calendar->symbol );
        $starts = [];

        foreach ($symbols_arr as $key => $symbol) {
          if ( is_numeric($symbol) ) {
            array_push( $starts, intval($symbol) );
          }
        }
        */

        //2
        // 8:24-16:34
        $starts = [];
        $regex = '/^([2][0-3]|[0-1]?[0-9])([:][0-5][0-9])?([-]([2][0-3]|[0-1]?[0-9])?([:][0-5][0-9])?)?$/'; // regexr.com/698qc
        if( preg_match( $regex, $calendar->symbol, $matches) ) {
          $symbols_arr = explode( '-', $calendar->symbol );
          foreach ($symbols_arr as $key => $symbol) {
            array_push( $starts, $symbol );
          }
        }

        return response()->json( $starts, 200 );
    }
}
