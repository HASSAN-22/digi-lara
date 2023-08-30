<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuaranteeRequest;
use App\Models\Guarantee;
use Illuminate\Http\Request;

class GuaranteeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Guarantee::class);
        $guarantees = Guarantee::search()->latest()->paginate(10);
        return response(['status'=>'success','data'=>$guarantees]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(GuaranteeRequest $request)
    {
        $this->authorize('create',Guarantee::class);
        $guarantee = Guarantee::create(['guarantee'=>$request->guarantee]);
        return $guarantee ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Guarantee $guarantee)
    {
        $this->authorize('view',$guarantee);
        return response(['status'=>'success','data'=>$guarantee]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guarantee $guarantee)
    {
        $this->authorize('update',$guarantee);
        $guarantee = $guarantee->update(['guarantee'=>$request->guarantee]);
        return $guarantee ? response(['status'=>'success'],201) : response(['status'=>'error'],500);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guarantee $guarantee)
    {
        $this->authorize('delete',$guarantee);
        return $guarantee->delete() ? response(['status'=>'success'],200) : response(['status'=>'error'],500);

    }
}
