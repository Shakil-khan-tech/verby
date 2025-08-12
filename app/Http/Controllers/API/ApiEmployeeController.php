<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Device;
use Illuminate\Http\Request;
use DB;
use Carbon\CarbonPeriod;
use Carbon\Carbon;

class ApiEmployeeController extends Controller
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
    public function get(Request $request)
    {
      $request->validate([
        'device_id' => 'sometimes|exists:devices,id',
      ]);
      
      if ( $request->has('device_id') ) {
        $employees = Device::find( request('device_id') )
        ->employees()
        ->active()
        ->select('id','name', 'surname', 'function as role', 'pin', 'card', 'camera', 'api_monitoring')
        ->get();
      } else {
        $employees = Employee::select('id','name', 'surname', 'function as role', 'pin', 'card', 'camera', 'api_monitoring')
        ->active()
        ->get();
      }
      // return Carbon::now()->subDay()->endOfDay();
      
        
        $meta = [
          "total" => count($employees),
        ];

        return response()->json( ['meta' => $meta, 'employees' => $employees], 200 );
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
        $request->validate([
          'pin' => 'required_without_all:card,camera',
          'card' => 'required_without_all:pin,camera',
          'camera' => 'required_without_all:pin,card',
        ]);

        if ( $request->has('pin') ) $employee->pin = request('pin');
        if ( $request->has('card') ) $employee->card = request('card');
        if ( $request->has('camera') ) $employee->camera = request('camera');

        $employee->save();
        $e = $employee->only(['id', 'name', 'surname', 'surname', 'pin', 'card', 'camera']);
        return response()->json( ['message' => 'updated sucessfully', 'employee' => $e], 200 );

    }
}
