<?php

namespace App\Services;

use App\Enums\AvailableEnum;
use App\Enums\StatusEnum;
use App\Models\Category;
use App\Models\Coupon;

class CouponService
{
    private static Coupon|null $coupon;

    /**
     * Find coupon
     * @param string $code
     * @return CouponService
     */
    public static function coupon(string $code): CouponService
    {
        self::$coupon = Coupon::where('code', $code)->whereDate('start_at',"<=", now())
            ->whereDate('expire_at','>',now())->with([
                'couponProducts'=>fn($q)=>$q->with('product'),
                'couponCategories'=>fn($q)=>$q->with('category'),
                'couponUsers',
            ])->first();
        return new self();
    }

    /**
     * Checks if the coupon is valid for the category or not
     * @return bool
     */
    public static function validForCategory(): bool
    {
        $categories = Category::whereRelation('products.baskets',fn($q)=>$q->where('user_id',6))->get(['id'])
            ->map(fn($category)=>array_map(fn($item)=>$item['id'],CategoryService::columns(['id'])->categoryParents($category)->get()));

        $categoryIds = array_unique(call_user_func_array('array_merge', $categories->toArray()));;
        return !is_null(self::$coupon->couponCategories()->whereIn('category_id',$categoryIds)->first());
    }

    /**
     * Checks if the coupon is valid for the product or not
     * @return bool
     */
    public static function validForProduct(): bool
    {
        $productIds = auth()->user()->baskets()->whereRelation('product',fn($q)=>$q->whereNull('amazing_offer_status')
            ->orWhere('amazing_offer_status',StatusEnum::PENDING)->whereNull('amazing_price'))
            ->get(['product_id'])->map(fn($item)=>$item['product_id'])->toArray();
        return !is_null(self::$coupon->couponProducts()->whereIn('product_id',$productIds)->first());
    }

    /**
     * Checks if the coupon is valid for the user or not
     * @return bool
     */
    public static function validForUser(): bool
    {
        return self::$coupon->count > self::$coupon->couponUsers()->where('user_id',auth()->Id())->get()->count();
    }

    /**
     * Checks if the coupon is valid or not
     * @return array|false[]
     */
    public static function isValid(): array
    {
        if(is_null(self::$coupon)){
            return ['status'=>false];
        }

        if((self::validForCategory() or self::validForProduct()) and self::validForUser()){
            self::addUserCoupon();
            return ['status'=>true,'type'=>self::$coupon->type, 'percent'=>self::$coupon->percent];
        }
        return ['status'=>false];
    }

    /**
     * Adds the number of coupon usage for the current user
     * @return void
     */
    private static function addUserCoupon(): void
    {
        self::$coupon->couponUsers()->create(['user_id'=>auth()->Id(),'is_available'=>AvailableEnum::AVAILABLE,'coupon_id'=>self::$coupon->id]);
    }


}
