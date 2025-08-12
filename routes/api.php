<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/register', 'Auth\UserAuthController@register');
// Route::post('/login', 'Auth\UserAuthController@login');

//Auth
// Route::match(['get', 'post'], '/register', 'Auth\UserAuthController@register');
Route::match(['get', 'post'], '/login', 'Auth\UserAuthController@login');
Route::match(['get', 'post'], '/logout', 'Auth\UserAuthController@destroy');


//Other
Route::middleware('auth:api')->group(function () {
    Route::match(['get', 'post'], 'employees/{device?}', 'API\ApiEmployeeController@get');
    Route::patch('employees/{employee}', 'API\ApiEmployeeController@update');
    Route::get('records/{employee}/get', 'API\ApiRecordController@get');
    Route::post('records/store', 'API\ApiRecordController@store');
    Route::post('records/bulkstore', 'API\ApiRecordController@bulk_store');
    Route::get('calendar/{device}', 'API\ApiCalendarController@get');
    Route::get('plan/{device}', 'API\ApiPlanController@get');
    Route::get('devices/{device}', 'API\ApiDeviceController@get');
    Route::get('devices/{device}/passcheck', 'API\ApiDeviceController@passcheck');
});
