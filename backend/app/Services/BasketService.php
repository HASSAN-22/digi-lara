<?php

namespace App\Services;

use App\Enums\AvailableEnum;
use App\Enums\CouponTypeEnum;
use App\Enums\StatusEnum;
use App\Models\Basket;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use  Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class BasketService
{

    private static Builder|Basket $query;
    private static Coupon|null $coupon;
    private static int $fullAmount = 0;
    private static int $originalAmount = 0;
    private static int $discount = 0;
    private static int $couponDiscount = 0;
    private static array $couponIds = [];
    private static string $couponType = '';

    public static function basket(Basket|null $basket = null){
        self::emptyData();
        Basket::whereRelation('product','count',0)->delete();
        self::$query = is_null($basket) ? new Basket() : $basket->where('id',$basket->id)->newQuery();
        return new self();
    }

    public static function findBy(...$args){
        self::$query = self::$query->whereIn('id',$args);
        return new self();
    }

    public static function forUser(int $userId){
        self::$query = self::$query->where('user_id',$userId);
        return new self();
    }

    public static function relations(array $relations){
        self::$query = self::$query->with($relations);
        return new self();
    }

    public static function calcAmount(bool $withCoupon = false){
        foreach ((clone self::$query)->get() as $basket){
            $product = $basket->product;
            $propertyAmount = self::getPropertyAmount($basket)['amount'];
            self::$originalAmount += $propertyAmount;
            self::$fullAmount += $propertyAmount;

            if($product->amazing_offer_status == StatusEnum::ACTIVE->value){
                $amount = getDiscount($product->price, $product->amazing_offer_percent, false);
                self::$fullAmount += $amount;
                self::$discount += $product->price - $amount;
            }elseif (!is_null($product->amazing_price)){
                self::$fullAmount += $product->amazing_price;
                self::$discount += $product->price - $product->amazing_price;
            }else{
                self::$fullAmount += $product->price;
                self::$coupon = $basket->coupon;
                if($withCoupon and self::$coupon){
                    self::categoryCoupon($product->category_id);
                    self::couponProducts($product->id);
                }
            }
            self::$fullAmount *= $basket->count;
            self::$originalAmount += ($product->price*$basket->count);
        }
        return new self();
    }

    public static function getPropertyAmount(Basket $basket)
    {
        $amount = 0;
        $type = '';
        $name = '';
        $colorCode = null;
        if (!is_null($basket->productSize)) {
            $amount += $basket->productSize->price;
            $type = 'size';
            $name = $basket->productSize->size->size;
        } else if (!is_null($basket->productColor)) {
            $color = $basket->productColor->color;
            $amount += $basket->productColor->price;
            $type = 'color';
            $colorCode = $color->color_code;
            $name = $color->color;
        }
        return ['amount'=>$amount, 'type'=>$type,'name'=>$name,'colorCode'=>$colorCode];
    }

    public static function categoryCoupon($categoryId){
        if(self::$coupon->couponCategories->filter(fn($cop)=>$cop->category_id == $categoryId)->count() > 0){
            self::calcCouponDiscount();
        }
    }

    /**
     * If there is a discount by product, it will be deducted from the price
     * @param Coupon $coupon
     * @param Product $product
     * @param int|float $amount
     * @return int|float
     */
    private static function couponProducts(int $productId):void{
        if(self::$coupon->couponProducts->filter(fn($cop) => $cop->product_id == $productId)->count() > 0){
            self::calcCouponDiscount();
        }
    }

    /**
     * Discount calculation
     * @param Coupon $coupon
     * @param bool $existData
     * @param int|float $amount
     * @return void
     */
    private static function calcCouponDiscount():void
    {
        $userCoupon = User::find(11)->couponUsers()->where('coupon_id',self::$coupon->id)->where('is_available',AvailableEnum::AVAILABLE)->first();
        if($userCoupon){
            self::$couponIds[] = $userCoupon->id;
            self::$couponType = self::$coupon->type;
            if (self::$coupon->type == CouponTypeEnum::PERCENT->value) {
                $result = getDiscount(self::$fullAmount, self::$coupon->percent, false);
                self::$discount += self::$fullAmount - $result;
                self::$couponDiscount += self::$fullAmount - $result;
                self::$fullAmount = $result;
            } else {
                self::$fullAmount -= self::$coupon->percent;
                self::$discount += self::$coupon->percent;
                self::$couponDiscount += self::$coupon->percent;
            }
        }
    }

    public static function get(...$args){
        $baskets = self::$query->get(...$args);
        $baskets['amount'] = [
            'discount'=>self::$discount,
            'fullAmount'=>self::$fullAmount,
            'couponDiscount'=>self::$couponDiscount,
            'originalAmount'=>self::$originalAmount,
            'couponIds'=>self::$couponIds,
            'couponType'=>self::$couponType,
        ];
        return $baskets;
    }

    /**
     * @return void
     */
    private static function emptyData(): void
    {
        self::$fullAmount = 0;
        self::$originalAmount = 0;
        self::$discount = 0;
        self::$couponDiscount = 0;
        self::$couponIds = [];
        self::$couponType = '';
    }

    public function __call(string $method, array|null $parameters=null)
    {

        return $parameters ? self::$query->$method($parameters) : self::$query->$method();
    }
/*

App\Services\BasketService::basket()->forUser(11)->relations([
'product'=>fn($q)=>$q->with([
    'guarantee'=>fn($q)=>$q->select('id','guarantee'),
    'brand'=>fn($q)=>$q->select('id','name'),
    'category'=>fn($q)=>$q->select('id','weight_type')
]),
'productSize'=>fn($q)=>$q->with('size'),
'productColor'=>fn($q)=>$q->with('color'),
'coupon'=>fn($q)=>$q->with(['couponProducts','couponCategories'=>fn($q)=>$q->with('category')])
])->get()

 */


//    private static Collection|int|float $baskets;
//    private static array $productColumns = ['*'];
//    private static int $discount = 0;
//    private static int $amount = 0;
//    private static int $originalAmount = 0;
//    private static array $couponIds = [];
//    private static array $productRelations = [];
//    private static bool $withCoupon = false;
//    private static string $discountType = '';
//    private static Authenticatable|User $user;
//
//    /**
//     * Filter product by this column(s)
//     * @param array $columns
//     * @return static
//     */
//    public static function setProductColumns(array $columns): static
//    {
//        self::$productColumns = $columns;
//        return new static();
//    }
//
//    /**
//     * Get product with relation(s)
//     * @param array $relations
//     * @return $this
//     */
//    public function productWith(array $relations): static
//    {
//        self::$productRelations = $relations;
//        return new static();
//    }
//
//    /**
//     * Get basket with coupons
//     * @param bool $withCoupon
//     * @return static
//     */
//    public static function withCoupon(bool $withCoupon = false){
//        self::$withCoupon = $withCoupon;
//        return new static();
//    }
//
//    /**
//     * Get basket items
//     * @param Authenticatable|User $user
//     * @return static
//     */
//    public static function baskets(Authenticatable|User $user): static
//    {
//        self::$user = $user;
//        self::$baskets = new Collection();
//        $relations = [
//            'product'=>fn($q)=>$q->select(self::$productColumns)->with(self::$productRelations),
//            'productSize'=>fn($q)=>$q->with('size'),
//            'productColor'=>fn($q)=>$q->with('color'),
//        ];
//        if(self::$withCoupon){
//            $relations['coupon'] = fn($q)=>$q->with(['couponProducts','couponCategories'=>fn($q)=>$q->with('category')]);
//        }
//        self::$baskets = $user->baskets()->with($relations)->get();
//        return new static();
//    }
//
//
//
//    /**
//     * Calculate the price of the shopping cart including discounts
//     * @return static
//     */
//    public static function calcAmount(): static
//    {
//        $fullAmount = 0;
//        foreach (self::$baskets as $basket) {
//            $product = $basket->product;
//            $propertyAmount = self::getPropertyAmount($basket)['amount'];
//
//            self::$originalAmount += $propertyAmount;
//            self::$amount += $propertyAmount;
//            if ($product->amazing_offer_status == 'yes') {
//                self::$amount += self::amazingOffer(self::$amount, $product);
//            } else if ($product->amazing_price != null) {
//                self::$amount += self::amazingPrice((int)$product->price, (int)$product->amazing_price);
//            } else {
//                self::$amount += $product->price;
//                $coupon = $basket->coupon;
//                if (self::$withCoupon and $coupon) {
//                    self::$amount += self::couponCategories($coupon, $product, self::$amount);
//                    self::$amount += self::couponProducts($coupon, $product, self::$amount);
//                }
//            }
//            self::$originalAmount += ($basket->count * $product->price);
//            self::$amount *= $basket->count;
//        }
//        Log::info(self::$amount);
//        return new static();
//    }
//
//    /**
//     * Calculate the discount of an item from the shopping cart
//     * @param Basket $basket
//     * @return array
//     */
//    public static function getDiscount(Basket $basket): array
//    {
//        $product = $basket->product;
//        $propertyAmount = self::getPropertyAmount($basket)['amount'];
//        $amount = $propertyAmount;
//        if ($product->amazing_offer_status == 'yes') {
//            self::$discountType = 'amazing_offer';
//            $amount = self::amazingOffer($amount, $product);
//        } else if ($product->amazing_price != null && $product->amazing_price > 0) {
//            self::$discountType = 'amazing_price';
//            $amount = self::amazingPrice((int)$product->price, (int)$product->amazing_price);
//        } else {
//            $amount = $product->price;
//            $coupon = $basket->coupon;
//            if ($coupon) {
//                $amount = self::couponCategories($coupon, $product, $amount);
//                $amount = self::couponProducts($coupon, $product, $amount);
//            }
//        }
//        $amount = ($amount * $basket->count);
//        return ['amount'=>$amount,'discount'=>self::$discount,'type'=>self::$discountType, 'couponIds'=>self::$couponIds];
//    }
//
//    /**
//     * Removes finished goods from the shopping cart
//     * @return static
//     */
//    public static function removeFinishedItems(): static
//    {
//        $productIds = [];
//        foreach(self::$baskets as $basket){
//            $product = $basket->product;
//            if($product->count <= 0){
//                $productIds[] = $product->id;
//            }
//        }
//        Product::whereIn('id',$productIds)->delete();
//        return new static();
//    }
//
//    /**
//     * Get product color or size price
//     * @param Basket $basket
//     * @return array
//     */
//    public static function getPropertyAmount(Basket $basket): array
//    {
//        $amount = 0;
//        $type = '';
//        $name = '';
//        $colorCode = null;
//        if ($basket->productSize != null) {
//            $amount += $basket->productSize->price;
//            $type = 'size';
//            $name = $basket->productSize->size->size;
//        } else if ($basket->productColor != null) {
//            $color = $basket->productColor->color;
//            $amount += $basket->productColor->price;
//            $type = 'color';
//            $colorCode = $color->color_code;
//            $name = $color->color;
//        }
//        return ['amount'=>$amount, 'type'=>$type,'name'=>$name,'colorCode'=>$colorCode];
//    }
//
//    /**
//     * If there is a discount by category, it will be deducted from the price
//     * @param Coupon $coupon
//     * @param Product $product
//     * @param int|float $amount
//     * @return int|float
//     */
//    private static function couponCategories(Coupon $coupon, Product $product, int|float $amount):int|float{
//        $categories = $coupon->couponCategories;
//        foreach ($categories as $category){
//            $_categoryIds = CategoryService::columns(['id'])->categoryChildes($category->category)->get();
//            $categoryIds = [];
//            foreach ($_categoryIds as $ids){
//                foreach ($ids as $id){
//                    $categoryIds[] = $id;
//                }
//            }
//            $_product = Product::whereIn('category_id',$categoryIds)->where('id',$product->id)->first();
//            $amount += self::calcCouponDiscount($coupon, !is_null($_product), $amount);
//        }
//        return $amount;
//    }
//
//    /**
//     * If there is a discount by product, it will be deducted from the price
//     * @param Coupon $coupon
//     * @param Product $product
//     * @param int|float $amount
//     * @return int|float
//     */
//    private static function couponProducts(Coupon $coupon, Product $product, int|float $amount):int|float{
//        $couponProduct = $coupon->couponProducts->filter(fn($cop) => $cop->product_id == $product->id);
//        return self::calcCouponDiscount($coupon, count($couponProduct) > 0, $amount);
//    }
//
//    /**
//     * Discount calculation
//     * @param Coupon $coupon
//     * @param bool $existData
//     * @param int|float $amount
//     * @return int|float
//     */
//    private static function calcCouponDiscount(Coupon $coupon, bool $existData, int|float $amount):int|float
//    {
//        $userCoupon = self::$user->couponUsers()->where('coupon_id',$coupon->id)->where('is_available',AvailableEnum::AVAILABLE)->first();
//        if ($existData and $userCoupon) {
//            self::$discountType = 'coupon';
//            self::$couponIds[] = $coupon->id;
//            if ($coupon->type == 'percent') {
//                $result = getDiscount($amount, $coupon->percent, false);
//                self::$discount += $amount - $result;
//                $amount = $result;
//            } else {
//                $amount = ($amount - $coupon->percent);
//                self::$discount += $coupon->percent;
//            }
//        }
//        return $amount;
//    }
//
//    /**
//     * Calculate amazing price
//     * @param int $productPrice
//     * @param int $amazingPrice
//     * @return float|int|mixed
//     */
//    private static function amazingPrice(int $productPrice, int $amazingPrice): mixed
//    {
//        self::$discount += $productPrice - $amazingPrice;
//        return $amazingPrice;
//    }
//
//    /**
//     * Calculate amazing offer
//     * @param mixed $amount
//     * @param mixed $product
//     * @return float|int|string
//     */
//    private static function amazingOffer(mixed $amount, mixed $product): string|int|float
//    {
//        $amount = getDiscount($amount, $product->amazing_offer_percent, false);
//        self::$discount += $amount;
//        return $amount;
//    }
//
//    /**
//     * Get result
//     * @return array
//     */
//    public static function get(): array
//    {
//        return ['baskets'=>self::$baskets,'amount'=>self::$amount, 'discount'=>self::$discount,'originalAmount'=>self::$originalAmount];
//    }


}
