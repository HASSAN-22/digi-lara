<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\FaqRequest;
use App\Models\Category;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Faq::class);
        $faqs = Faq::latest()->with(['category'=>fn($q)=>$q->select('id','title')])->search()->paginate(10);
        $categorie = Category::where('type','faq')->where('status',StatusEnum::ACTIVE)->get();
        return response(['status'=>'success','data'=>['faqs'=>$faqs,'categories'=>$categorie]]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaqRequest $request)
    {
        $this->authorize('create',Faq::class);
        $faq = Faq::create(['category_id'=>$request->category_id,'title'=>$request->title,'description'=>$request->description]);
        return $faq ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq)
    {
        $this->authorize('view',$faq);
        return response(['status'=>'success', 'data'=>$faq]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(FaqRequest $request, Faq $faq)
    {
        $this->authorize('update',$faq);
        $faq = $faq->update(['category_id'=>$request->category_id,'title'=>$request->title,'description'=>$request->description]);
        return $faq ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $this->authorize('delete',$faq);
        return $faq->delete() ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }
}
