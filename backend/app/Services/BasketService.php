<?php

namespace App\Services;

use App\Enums\AvailableEnum;
use App\Models\Basket;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use  Illuminate\Contracts\Auth\Authenticatable;

class BasketService
{
    private static Collection|int|float $baskets;
    private static array $productColumns = ['*'];
    private static int $discount = 0;
    private static int $amount = 0;
    private static int $originalAmount = 0;
    private static array $couponIds = [];
    private static array $productRelations = [];
    private static bool $withCoupon = false;
    private static string $discountType = '';
    private static Authenticatable|User $user;

    /**
     * Filter product by this column(s)
     * @param array $columns
     * @return static
     */
    public static function setProductColumns(array $columns): static
    {
        self::$productColumns = $columns;
        return new static();
    }

    /**
     * Get product with relation(s)
     * @param array $relations
     * @return $this
     */
    public function productWith(array $relations): static
    {
        self::$productRelations = $relations;
        return new static();
    }

    /**
     * Get basket with coupons
     * @param bool $withCoupon
     * @return static
     */
    public static function withCoupon(bool $withCoupon = false){
        self::$withCoupon = $withCoupon;
        return new static();
    }

    /**
     * Get basket items
     * @param Authenticatable|User $user
     * @return static
     */
    public static function baskets(Authenticatable|User $user): static
    {
        self::$user = $user;
        self::$baskets = new Collection();
        $relations = [
            'product'=>fn($q)=>$q->select(self::$productColumns)->with(self::$productRelations),
            'productSize'=>fn($q)=>$q->with('size'),
            'productColor'=>fn($q)=>$q->with('color'),
        ];
        if(self::$withCoupon){
            $relations['coupon'] = fn($q)=>$q->with(['couponProducts','couponCategories'=>fn($q)=>$q->with('category')]);
        }
        self::$baskets = $user->baskets()->with($relations)->get();
        return new static();
    }



    /**
     * Calculate the price of the shopping cart including discounts
     * @return static
     */
    public static function calcAmount(): static
    {
        foreach (self::$baskets as $basket) {
            $product = $basket->product;
            $propertyAmount = self::getPropertyAmount($basket)['amount'];
            self::$originalAmount += $propertyAmount;
            $amount = $propertyAmount;
            if ($product->amazing_offer_status == 'yes') {
                $amount = self::amazingOffer($amount, $product);
            } else if ($product->amazing_price != null) {
                $amount = self::amazingPrice((int)$product->price, (int)$product->amazing_price);
            } else {
                $coupon = $basket->coupon;
                if (self::$withCoupon and $coupon) {
                    $amount = self::couponCategories($coupon, $product, $amount);
                    $amount = self::couponProducts($coupon, $product, $amount);
                }else{
                    $amount = $product->price;
                }
            }
            self::$originalAmount += ($basket->count * $product->price);
            $amount = $amount * $basket->count;
            self::$amount += $amount;
        }
        return new static();
    }

    /**
     * Calculate the discount of an item from the shopping cart
     * @param Basket $basket
     * @return array
     */
    public static function getDiscount(Basket $basket): array
    {
        $product = $basket->product;
        $propertyAmount = self::getPropertyAmount($basket)['amount'];
        $amount = $propertyAmount;
        if ($product->amazing_offer_status == 'yes') {
            self::$discountType = 'amazing_offer';
            $amount = self::amazingOffer($amount, $product);
        } else if ($product->amazing_price != null && $product->amazing_price > 0) {
            self::$discountType = 'amazing_price';
            $amount = self::amazingPrice((int)$product->price, (int)$product->amazing_price);
        } else {
            $coupon = $basket->coupon;
            if ($coupon) {
                $amount = self::couponCategories($coupon, $product, $amount);
                $amount = self::couponProducts($coupon, $product, $amount);
            }else{
                $amount = $product->price;
            }
        }
        $amount = ($amount * $basket->count);
        return ['amount'=>$amount,'discount'=>self::$discount,'type'=>self::$discountType, 'couponIds'=>self::$couponIds];
    }

    /**
     * Removes finished goods from the shopping cart
     * @return static
     */
    public static function removeFinishedItems(): static
    {
        $productIds = [];
        foreach(self::$baskets as $basket){
            $product = $basket->product;
            if($product->count <= 0){
                $productIds[] = $product->id;
            }
        }
        Product::whereIn('id',$productIds)->delete();
        return new static();
    }

    /**
     * Get product color or size price
     * @param Basket $basket
     * @return array
     */
    public static function getPropertyAmount(Basket $basket): array
    {
        $amount = 0;
        $type = '';
        $name = '';
        $colorCode = null;
        if ($basket->productSize != null) {
            $amount += $basket->productSize->price;
            $type = 'size';
            $name = $basket->productSize->size->size;
        } else if ($basket->productColor != null) {
            $color = $basket->productColor->color;
            $amount += $basket->productColor->price;
            $type = 'color';
            $colorCode = $color->color_code;
            $name = $color->color;
        }
        return ['amount'=>$amount, 'type'=>$type,'name'=>$name,'colorCode'=>$colorCode];
    }

    /**
     * If there is a discount by category, it will be deducted from the price
     * @param Coupon $coupon
     * @param Product $product
     * @param int|float $amount
     * @return int|float
     */
    private static function couponCategories(Coupon $coupon, Product $product, int|float $amount):int|float{
        $categories = $coupon->couponCategories;
        foreach ($categories as $category){
            $_categoryIds = CategoryService::columns(['id'])->categoryChildes($category->category)->get();
            $categoryIds = [];
            foreach ($_categoryIds as $ids){
                foreach ($ids as $id){
                    $categoryIds[] = $id;
                }
            }
            $_product = Product::whereIn('category_id',$categoryIds)->where('id',$product->id)->first();
            $amount += self::calcCouponDiscount($coupon, !is_null($_product), $amount);
        }
        return $amount;
    }

    /**
     * If there is a discount by product, it will be deducted from the price
     * @param Coupon $coupon
     * @param Product $product
     * @param int|float $amount
     * @return int|float
     */
    private static function couponProducts(Coupon $coupon, Product $product, int|float $amount):int|float{
        $couponProduct = $coupon->couponProducts->filter(fn($cop) => $cop->product_id == $product->id);
        return self::calcCouponDiscount($coupon, count($couponProduct) > 0, $amount);
    }

    /**
     * Discount calculation
     * @param Coupon $coupon
     * @param bool $existData
     * @param int|float $amount
     * @return int|float
     */
    private static function calcCouponDiscount(Coupon $coupon, bool $existData, int|float $amount):int|float
    {
        $userCoupon = self::$user->couponUsers()->where('coupon_id',$coupon->id)->where('is_available',AvailableEnum::AVAILABLE)->first();
        if ($existData and $userCoupon) {
            self::$discountType = 'coupon';
            self::$couponIds[] = $coupon->id;
            if ($coupon->type == 'percent') {
                $result = getDiscount($amount, $coupon->percent, false);
                self::$discount += $amount - $result;
                $amount = $result;
            } else {
                $amount = ($amount - $coupon->percent);
                self::$discount += $coupon->percent;
            }
        }
        return $amount;
    }

    /**
     * Calculate amazing price
     * @param int $productPrice
     * @param int $amazingPrice
     * @return float|int|mixed
     */
    private static function amazingPrice(int $productPrice, int $amazingPrice): mixed
    {
        self::$discount += $productPrice - $amazingPrice;
        return $amazingPrice;
    }

    /**
     * Calculate amazing offer
     * @param mixed $amount
     * @param mixed $product
     * @return float|int|string
     */
    private static function amazingOffer(mixed $amount, mixed $product): string|int|float
    {
        $amount = getDiscount($amount, $product->amazing_offer_percent, false);
        self::$discount += $amount;
        return $amount;
    }

    /**
     * Get result
     * @return array
     */
    public static function get(): array
    {
        return ['baskets'=>self::$baskets,'amount'=>self::$amount, 'discount'=>self::$discount,'originalAmount'=>self::$originalAmount];
    }


}
