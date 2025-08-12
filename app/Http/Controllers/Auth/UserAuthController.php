<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Device;
use Illuminate\Http\Request;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $data['password'] = bcrypt($request->password);

        $user = User::create($data);

        $token = $user->createToken('API Token')->accessToken;

        return response([ 'user' => $user, 'token' => $token]);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if (!auth()->attempt($data)) {
            return response(['error_message' => 'Incorrect Details. Please try again'], 400);
        }
        
        $token = auth()->user()->createToken('API Token')->accessToken;
        
        $device = Device::where('user_id', auth()->user()->id)->first();
        
        if ( $device ) {
            return response(['user' => auth()->user()->only(['id', 'email']), 'token' => $token, 'device_id' => $device->id]);
        } else {
            return response(['error_message' => 'Login is not associated with device'], 400);
        }
        


    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        auth()->user()->revoke();
        return response( ['Logged out'], 200 );
    }
    
}
