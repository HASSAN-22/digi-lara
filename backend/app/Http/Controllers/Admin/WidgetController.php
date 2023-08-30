<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PublishEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Widget;
use Illuminate\Http\Request;

class WidgetController extends Controller
{
    /**
     * Display the specified resource.
     * @param Widget|null $widget
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Widget|null $widget=null){
        if($widget){
            $this->authorize('view',$widget);
        }else{
            $this->authorize('viewAny',Widget::class);
            $widget = Widget::first();
        }
        $products = Product::where('publish',PublishEnum::PUBLISHED->value)->whereHas('user',fn($q)=>$q->where('status','yes')->where('access','!=','user'))->get();
        $categories = Category::where('status',StatusEnum::ACTIVE->value)->get();
        $brands = Brand::where('status',StatusEnum::ACTIVE->value)->get();
        return response(['status'=>'success','data'=>['widgets'=>$widget, 'products'=>$products,'categories'=>$categories,'brands'=>$brands]]);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request){
        $this->authorize('create',Widget::class);
        $widget = Widget::updateOrCreate(['page'=>$request->page],[
            'page'=>$request->page,
            'data'=>json_encode($request->data)
        ]);
        return $widget ? response(['status'=>'success'],201) : response(['status'=>'error'],500);

    }

    public function getProduct(Category $category){
        $this->authorize('viewAny',Widget::class);
        return response(['status'=>'success','data'=>$category->products]);
    }
}
