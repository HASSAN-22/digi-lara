<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Color::class);
        $colors = Color::latest()->paginate(10);
        return response(['status'=>'success','data'=>$colors]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'color'=>['required','string','max:255'],
            'color_code'=>['required','string','max:255'],
        ]);
        $this->authorize('create',Color::class);
        $color = Color::create(['color'=>$request->color,'color_code'=>$request->color_code]);
        return $color ? response(['status'=>'success'],200) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Color $color)
    {
        $this->authorize('view',$color);
        return response(['status'=>'success','data'=>$color]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Color $color)
    {
        $request->validate([
            'color'=>['required','string','max:255'],
            'color_code'=>['required','string','max:255'],
        ]);
        $this->authorize('update',$color);
        $color = $color->update(['color'=>$request->color,'color_code'=>$request->color_code]);
        return $color ? response(['status'=>'success'],200) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Color $color)
    {
        $this->authorize('delete',$color);
        return $color->delete() ? response(['status'=>'success'],200) : response(['status'=>'error'],500);
    }
}
