<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductCommentResource;
use App\Models\Productcomment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ProductCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Productcomment::class);
        $productComments = Productcomment::latest()->with([
            'product'=>fn($q)=>$q->select('id','ir_name'),
            'user'=>fn($q)=>$q->select('id','name')
        ])->search()->paginate(10);
        return ProductCommentResource::collection($productComments);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Productcomment $productcomment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Productcomment $productcomment)
    {
        $this->authorize('update',$productcomment);
        $request->validate(['status'=>['required','string','max:255',new Enum(StatusEnum::class)]]);
        $productcomment = $productcomment->update(['status'=>StatusEnum::from($request->status)]);
        return $productcomment ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Productcomment $productcomment)
    {
        //
    }
}
