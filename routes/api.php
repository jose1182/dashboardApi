<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::group(['middleware' => ['web']], function () {
    Route::get('auth/redirect', function () {
        return Socialite::driver('google')->redirect();
    });
    
    Route::get('auth/callback', function () {
     
        $user = Socialite::driver('google')->user();
        return ('ser');
        // $user->token
    });
    
});

//Rutas email verification
Route::get('email/verify/{id}', 'App\Http\Controllers\VerificationController@verify')->name('verification.verify'); // Make sure to keep this as your route name
Route::get('email/resend', 'App\Http\Controllers\VerificationController@resend')->name('verification.resend');


//Rutas a las que se accederán para registrarse e iniciar sesión
Route::post('register', 'App\Http\Controllers\UserController@register');
Route::post('login', 'App\Http\Controllers\UserController@authenticate');



//Creamos un group para indicar cuales son las rutas que necesitan autenticación:
Route::group(['middleware' => ['jwt.verify']], function() {

    Route::post('user','App\Http\Controllers\UserController@getAuthenticatedUser');

});

