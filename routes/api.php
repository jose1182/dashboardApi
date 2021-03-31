<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitiesController;
use App\Http\Controllers\WeatherController;
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

Route::get('testing', function(){
    return (['user' => 'jose carlos']);
});


//Rutas Login Socialite
Route::group(['middleware' => ['web']], function () {
    Route::get('auth/{driver}/redirect', 'App\Http\Controllers\UserController@redirectToProvider');
    Route::get('auth/{driver}/callback', 'App\Http\Controllers\UserController@handleProviderCallback');
    
});

//Rutas email verification
Route::get('email/verify/{id}', 'App\Http\Controllers\VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend', 'App\Http\Controllers\VerificationController@resend')->name('verification.resend');


//Rutas a las que se accederán para registrarse e iniciar sesión
Route::post('register', 'App\Http\Controllers\UserController@register');
Route::post('login', 'App\Http\Controllers\UserController@authenticate');


//test
Route::apiResource('city', CitiesController::class);



//Creamos un group para indicar cuales son las rutas que necesitan autenticación:
Route::group(['middleware' => ['jwt.verify']], function() {
    
    Route::apiResource('widget/weather', WeatherController::class);
    // Route::post('user','App\Http\Controllers\UserController@getAuthenticatedUser');
    Route::get('post', function(){
        return ("hello");
    });

});

