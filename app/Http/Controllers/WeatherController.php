<?php

namespace App\Http\Controllers;

use JWTAuth;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\WidgetWeather;

class WeatherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $widgets = WidgetWeather::all();
        return($widgets);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'city' => 'required'
        ]);

     
        if($validator->fails()){
            return response()->json([$validator->erros()->toJson()],400);
        }

        $newWeather = WidgetWeather::create([
            'name' => $request->get('name'),
            'api_url' => env('URL_API_WEATHER') . $request->get('city') . '&appid=3f5c2b287c7c5833f7b9fbc49bd0103f',
            'user_id' => JWTAuth::user()->id,
        ]);

        return ($newWeather);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return WidgetWeather::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $widget = WidgetWeather::find($id);
        $widget->update($request->all());
        return $widget;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return WidgetWeather::destroy($id);
    }
}
