<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CouponTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Http\Resources\CouponResource;
use App\Jobs\NewsletterJob;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny',Coupon::class);
        $coupons = Coupon::latest()->search()->paginate(10);
        return CouponResource::collection($coupons);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CouponRequest $request)
    {
        $this->authorize('create',Coupon::class);
        DB::beginTransaction();
        try{
            $coupon = Coupon::create([
                'title'=>$request->title,
                'code'=>$request->code,
                'type'=>CouponTypeEnum::from($request->type),
                'percent'=>$request->percent,
                'count'=>$request->count,
                'start_at'=>$request->start_at,
                'expire_at'=>$request->expire_at,
            ]);
            if(count($request->categories) > 0){
                $coupon->syncCouponCategories()->attach($request->categories);
            }

            if(count($request->products) > 0){
                $coupon->syncCouponProducts()->attach($request->products);
            }
            $discount = $coupon->type == 'fixed' ? "مبلغ تخفیف (".$coupon->percent.") ریال" : "درصد تخفیف (".$coupon->percent."%)";
            $date = dateToPersian($coupon->start_at);
            $message = "کاربر گرامی یک کد تخفیف با عنوان
            ({$coupon->title}) با کد ({$coupon->code})
             و
             {$discount}
              فعال شده است شما میتوانید در ({$date}) از این تخفیف استفاده نمایید";
            dispatch(new NewsletterJob($message,config('app.name') . '  کد تخفیف جدید '));
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','err'=>$e->getMessage()],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Coupon $coupon)
    {
        $this->authorize('view',$coupon);
        $coupon = $coupon->where('id',$coupon->id)->with([
            'couponProducts'=>fn($q)=>$q->with(['product'=>fn($q)=>$q->select('id','ir_name')]),
            'couponCategories'=>fn($q)=>$q->with(['category'=>fn($q)=>$q->select('id','title')]),
        ])->first();
        return response(['status'=>'success','data'=>$coupon]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CouponRequest $request, Coupon $coupon)
    {
        $this->authorize('update',$coupon);
        DB::beginTransaction();
        try{
            $coupon->update([
                'title'=>$request->title,
                'code'=>$request->code,
                'type'=>CouponTypeEnum::from($request->type),
                'percent'=>$request->percent,
                'count'=>$request->count,
                'start_at'=>$request->start_at,
                'expire_at'=>$request->expire_at,
            ]);
            if(count($request->categories) > 0){
                $coupon->couponProducts()->delete();
                $coupon->syncCouponCategories()->sync($request->categories);
            }

            if(count($request->products) > 0){
                $coupon->couponCategories()->delete();
                $coupon->syncCouponProducts()->sync($request->products);
            }

            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','err'=>$e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $this->authorize('delete',$coupon);
        return $coupon->delete() ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getCategories(){
        $categories = Category::where('status',StatusEnum::ACTIVE)->get(['id','title']);
        return response(['status'=>'success','data'=>$categories]);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function getProducts(){
        $products = Product::where('publish',PublishEnum::PUBLISHED)->get(['id','ir_name']);
        return response(['status'=>'success','data'=>$products]);
    }
}
