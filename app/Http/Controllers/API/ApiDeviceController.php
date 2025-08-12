<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use DB;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Hash;

class ApiDeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
        // $this->authorizeResource('user');
    }


    /**
     * Get device in json format.
     *
     * @return json
     */
    public function get(Device $device, Request $request)
    {
        $request->validate([
          'include' => 'in:rooms',
        ]);

        if ( $request->has('include') ) {
          $device = Device::where('id', $device->id)
          ->select('id', 'name')
          ->with('rooms:id,name,category,device_id')
          ->first();
        } else {
          $device = Device::where('id', $device->id)
          ->select('id', 'name')
          ->first();
        }

        return response()->json( $device, 200 );
    }

    /**
     * Get device in json format.
     *
     * @return json
     */
    public function passcheck(Device $device, Request $request)
    {
        // return response()->json( [$device->user], 200 );
        $request->validate([
          // 'username' => 'required',
          'password' => 'required',
        ]);

        $pass = Hash::make( request('password') );
        // $pass = bcrypt( request('password') );
        // return response()->json( $pass, 200 );

        if (Hash::check( request('password'), $device->user->password )) {
          return response()->json( true, 200 );
        } else {
          return response()->json( false, 200 );
        }
    }
}
