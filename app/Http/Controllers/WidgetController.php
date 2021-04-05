<?php

namespace App\Http\Controllers;
use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Widget;
class WidgetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // get all post
        $user = JWTAuth::user();
        $widgets = Widget::where('user_id',"=", $user->id)->get();
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
            'param_1' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()->toJson()], 400);
        }

        $newWidget = Widget::create([
            'name' => $request->get('name'),
            'param_1' => $request->get('param_1'),  
            'param_2' => $request->get('param_2'), 
            'param_3' => $request->get('param_3'), 
            'user_id' => JWTAuth::user()->id,
        ]);

        return ($newWidget);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //show widget
        return Widget::find($id);
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
        //update widget
        $widget = Widget::find($id);
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
        //delte widget
        return Widget::destroy($id);
    }
}
