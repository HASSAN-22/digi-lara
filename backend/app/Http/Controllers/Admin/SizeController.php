<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Size::class);
        $sizes = Size::latest()->paginate(10);
        return response(['status'=>'success','data'=>$sizes]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['size'=>['required','string','max:255']]);
        $this->authorize('create',Size::class);
        $size = Size::create(['size'=>$request->size]);
        return $size ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Size $size)
    {
        $this->authorize('view',$size);
        return response(['status'=>'success','data'=>$size]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Size $size)
    {
        $request->validate(['size'=>['required','string','max:255']]);
        $this->authorize('update',$size);
        $size = $size->update(['size'=>$request->size]);
        return $size ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Size $size)
    {
        $this->authorize('delete',$size);
        return $size->delete() ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }
}
