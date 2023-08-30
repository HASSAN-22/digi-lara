<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Page::class);
        $pages = Page::latest()->search()->with(['user'=>fn($q)=>$q->select('id','name')])->paginate(10);
        return PageResource::collection($pages);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(PageRequest $request)
    {
        $this->authorize('create',Page::class);
        $page = Page::create([
            'user_id'=>auth()->Id(),
            'title'=>$request->title,
            'description'=>$request->description
        ]);
        return $page ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        $this->authorize('view',$page);
        return response(['status'=>'success','data'=>$page]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(PageRequest $request, Page $page)
    {
        $this->authorize('update',$page);
        $page = $page->update([
            'user_id'=>auth()->Id(),
            'title'=>$request->title,
            'description'=>$request->description
        ]);
        return $page ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        $this->authorize('delete',$page);
        return $page->delete() ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }
}
