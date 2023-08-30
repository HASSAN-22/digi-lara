<?php

namespace App\Services;

use App\Enums\PublishEnum;
use App\Enums\StatusEnum;
use App\Enums\UserAccessEnum;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ProductService
{
    private static Builder|Product $query;

    /**
     * Make an object of product
     * @return ProductService
     */
    public static function product(){
        self::$query = new Product();
        return new self();
    }

    /**
     * Add a condition to query
     * @param ...$args
     * @return ProductService
     */
    public static function where(...$args){
        self::$query = self::$query->where($args);
        return new self();
    }

    /**
     * Adds a condition for checking published products
     * @return ProductService
     */
    public static function published(){
        self::where(['publish',PublishEnum::PUBLISHED]);
        return new self();
    }

    /**
     * Adds a condition for checking drafted products
     * @return ProductService
     */
    public static function draft(){
        self::where(['publish',PublishEnum::DRAFT]);
        return new self();
    }

    /**
     * Checks that the seller of the current products is active
     * @return ProductService
     */
    public static function activeSeller(){
        self::$query = self::$query->whereRelation('user',fn($q)=>$q->where('status',StatusEnum::ACTIVE)->where('access',UserAccessEnum::SELLER));
        return new self();
    }

    /**
     * Receive products with ID(s)
     * @param ...$args
     * @return ProductService
     */
    public static function findById(...$args){
        self::$query = self::$query->whereIn('id',$args);
        return new self();
    }

    /**
     * Receive products with custom column
     * @param string $column
     * @param ...$args
     * @return ProductService
     */
    public static function findBy(string $column, ...$args){
        self::$query = self::$query->whereIn($column,$args);
        return new self();
    }

    /**
     * Receive products with slug
     * @param string $slug
     * @return ProductService
     */
    public static function findBySlug(string $slug){
        self::where(['slug',$slug]);
        return new self();
    }

    /**
     * Add relations to query
     * @param array $relations
     * @return ProductService
     */
    public static function withRelations(array $relations){
        self::$query = self::$query->with($relations);
        return new self();
    }

    /**
     * Add a query condition based on category ID(s)
     * @param array $categoryIds
     * @return ProductService
     */
    public static function byCategory(array $categoryIds){
        self::$query = self::$query->whereIn('category_id',$categoryIds);
        return new self();
    }

    /**
     * Filter products based on whether they are in the range of these two prices
     * @param int $minPrice
     * @param int $maxPrice
     * @return ProductService
     */
    public static function filterByPrice(int $minPrice, int $maxPrice){
        self::$query = self::$query->whereBetween('price',[$minPrice, $maxPrice]);
        return new self();
    }

    /**
     * Filter products based on colors availability or absence
     * @param array $colorIds
     * @return ProductService
     */
    public static function filterByColor(array $colorIds){
        self::$query = self::$query->whereRelation('productColors',fn($q)=>$q->whereIn('color_id',$colorIds));
        return new self();
    }

    /**
     * Filter products based on sizes availability or absence
     * @param array $sizeIds
     * @return ProductService
     */
    public static function filterBySize(array $sizeIds){
        self::$query = self::$query->whereRelation('productSizes',fn($q)=>$q->whereIn('size_id',$sizeIds));
        return new self();
    }

    /**
     * Filter products based on properties availability or absence
     * @param array $propertyIds
     * @return ProductService
     */
    public static function FilterByProperties(array $propertyIds){
        self::$query = self::$query->whereRelation('productProperties',fn($q)=>$q->whereIn('propertytype_id',$propertyIds));
        return new self();
    }

    /**
     * Filter products based on whether or not they are in stock
     * @param bool $available
     * @return ProductService
     */
    public static function available(bool $available){
        return $available ? self::isAvailables() : self::notAvailable();
    }

    /**
     * Add a query condition based on the number of products not being exhausted
     * @return ProductService
     */
    public static function isAvailables(){
        self::where(['count','>','0']);
        return new self();
    }

    /**
     * Add a query condition based on the number of out-of-stock products
     * @return ProductService
     */
    public static function notAvailable(){
        self::where(['count','<=','0']);
        return new self();
    }

    /**
     * Sort products by views
     * @return ProductService
     */
    public static function topVisit(){
        self::$query = self::$query->withCount('visits')->orderByDesc('visits_count');
        return new self();
    }

    /**
     * Get the latest products
     * @return ProductService
     */
    public static function newest(){
        self::$query = self::$query->latest('created_at');
        return new self();
    }

    /**
     * Get the oldest products
     * @return ProductService
     */
    public static function oldest(){
        return new self();
    }

    /**
     * Get bestselling products
     * @return ProductService
     */
    public static function bestsellings(){
        self::withRelations(['orderDetails'=>fn($q)=>$q->select('*',DB::raw('SUM(count) as sum'))->groupBy('product_id')]);
        self::$query = self::$query->has('orderDetails');
        return new self();
    }

    /**
     * Get cheapest products
     * @return ProductService
     */
    public static function cheapest(){
        self::$query = self::$query->orderBy('amazing_price')->orderBy('price');
        return new self();
    }

    /**
     * Get expensive products
     * @return ProductService
     */
    public static function expensive(){
        self::$query = self::$query->orderByDesc('amazing_price')->orderByDesc('price');
        return new self();
    }

    public function __call(string $method, mixed $parameters=null)
    {
        return $parameters ? self::$query->$method($parameters) : self::$query->$method();
    }

}
