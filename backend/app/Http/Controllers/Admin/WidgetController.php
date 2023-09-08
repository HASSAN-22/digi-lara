<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CategoryTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Widget;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $products = ProductService::product()->published()->activeSeller()->where(['amazing_offer_status','=','yes'])->get();
        $categories = CategoryService::category()->active()->where(['type',CategoryTypeEnum::CATEGORY])->get();
        $parents = (clone $categories)->where('parent_id','0')->values();
        $categoryHaveProducts = (clone $categories)->filter(fn($item)=>$item->children->count() <= 0)->values();
        $brands = Brand::where('status',StatusEnum::ACTIVE->value)->get();
        return response(['status'=>'success','data'=>['widgets'=>$widget, 'products'=>$products,'categories'=>$categories,'parents'=>$parents,'category_have_products'=>$categoryHaveProducts,'brands'=>$brands]]);
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
