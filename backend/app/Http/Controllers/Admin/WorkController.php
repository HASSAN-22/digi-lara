<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Work;
use Illuminate\Http\Request;

class WorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Work::class);
        $works = Work::latest()->search()->paginate(10);
        return response(['status'=>'success','data'=>$works]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create',Work::class);
        $request->validate(['work_name'=>['required','string','max:255']]);
        $work = Work::create(['name'=>$request->work_name]);
        return $work ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * @param Work $work
     * Display the specified resource.
     */
    public function show(Work $work)
    {
        $this->authorize('view',$work);
        return response(['status'=>'success','data'=>$work]);
    }


    /**
     * @param Work $work
     * Update the specified resource in storage.
     */
    public function update(Request $request, Work $work)
    {
        $this->authorize('update',$work);
        $request->validate(['work_name'=>['required','string','max:255']]);
        $work = $work->update(['name'=>$request->work_name]);
        return $work ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * @param Work $job
     * Remove the specified resource from storage.
     */
    public function destroy(Work $work)
    {
        $this->authorize('delete',$work);
        return $work->delete() ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }
}
