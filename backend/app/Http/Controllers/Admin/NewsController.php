<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewsRequest;
use App\Http\Resources\NewsResource;
use App\Models\Category;
use App\Models\News;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    private string $DIRECTORY = 'uploader/news';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewany',News::class);
        $news = News::latest()->search()->with([
            'category'=>fn($q)=>$q->select('id','title'),
            'user'=>fn($q)=>$q->select('id','name'),
        ])->paginate(10);
        $categories = Category::where('type','news')->where('status',StatusEnum::ACTIVE)->get();
        return NewsResource::collection($news)->additional(['categories'=>$categories]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NewsRequest $request)
    {
        $this->authorize('create',News::class);
        $news = News::create([
            'category_id'=>$request->category_id,
            'user_id'=>auth()->Id(),
            'title'=>$request->title,
            'slug'=>slug($request->title),
            'short_description'=>$request->short_description,
            'description'=>$request->description,
            'image'=>Upload::upload($request,'image',$this->DIRECTORY),
            'publish'=>$request->publish,
        ]);
        return $news ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news)
    {
        $this->authorize('view',$news);
        return response(['status'=>'success','data'=>$news]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(NewsRequest $request, News $news)
    {
        $this->authorize('update',$news);
        $news = $news->update([
            'category_id'=>$request->category_id,
            'user_id'=>auth()->Id(),
            'title'=>$request->title,
            'slug'=>slug($request->title),
            'short_description'=>$request->short_description,
            'description'=>$request->description,
            'image'=>Upload::upload($request,'image',$this->DIRECTORY,$news->image),
            'publish'=>$request->publish,
        ]);
        return $news ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        $this->authorize('delete',$news);
        if($news->delete()){
            removeFile($news->image);
            return response(['status'=>'success']);
        }
        return response(['status'=>'error'],500);
    }
}
