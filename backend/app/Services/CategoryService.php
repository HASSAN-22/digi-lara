<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class CategoryService
{

    private static Category|Builder $query;
    private static Collection $data;

    /**
     * Make an object of category
     * @return CategoryService
     */
    public static function category(){
        self::$data = new Collection();
        self::$query = new Category();
        return new self();
    }

    /**
     * Add a condition to query
     * @param ...$args
     * @return CategoryService
     */
    public static function where(...$args){
        self::$query = self::$query->where($args);
        return new self();
    }

    /**
     * Adds a condition for checking active categories
     * @return CategoryService
     */
    public static function active(){
        self::where(['status',StatusEnum::ACTIVE]);
        return new self();
    }

    /**
     * Adds a condition for checking deactivated categories
     * @return CategoryService
     */
    public static function deactivated(){
        self::where(['status',StatusEnum::DEACTIVATED]);
        return new self();
    }

    /**
     * Adds a condition for checking pending categories
     * @return CategoryService
     */
    public static function pending(){
        self::where(['status',StatusEnum::PENDING]);
        return new self();
    }

    /**
     * Receive category with ID(s)
     * @param ...$args
     * @return CategoryService
     */
    public static function findById(...$args){
        self::$query = self::$query->whereIn('id',$args);
        return new self();
    }

    /**
     * Receive category with slug
     * @param string $slug
     * @return CategoryService
     */
    public static function findBySlug(string $slug){
        self::where(['slug',$slug]);
        return new self();
    }

    /**
     * Receive category with custom column
     * @param array $relations
     * @return CategoryService
     */
    public static function withRelations(array $relations){
        self::$query = self::$query->with($relations);
        return new self();
    }

    /**
     * Get category parents
     * @param ...$args
     * @return CategoryService|Collection
     */
    public static function parents(...$args){
        self::$query = self::$query->with('category');
        if(count($args) > 0){
            self::filterNestedColumn($args,'category', self::$query->first());
            return self::$data;
        }
        return new self();
    }

    /**
     * Get category childrens
     * @param ...$args
     * @return CategoryService|Collection
     */
    public static function childrens(...$args){
        self::$query = self::$query->with('children');
        if(count($args) > 0){
            self::filterNestedColumn($args,'children', self::$query->get());
            return self::$data;
        }
        return new self();
    }

    /**
     * Get all category properties
     * @param bool $withProperty
     * @param bool $withPropertyType
     * @return CategoryService
     */
    public static function properties(bool $withProperty=false, bool $withPropertyType = false){
        $ids = self::parents('id');
        self::category()->findById(...$ids)->withRelations(['categoryProperties'=>function($q)use($withProperty, $withPropertyType){
            if($withProperty){
                $q->with($withPropertyType ? ['property'=>fn($query)=>$query->with('propertyTypes')] : 'property');
            }
        }]);
        self::$query = self::$query->has('categoryProperties');
        return new self();
    }

    /**
     * Scroll categories to filter one or more columns
     * @param array $columns
     * @param string $relation
     * @param mixed|null $categoryData
     * @return void
     */
    private static function filterNestedColumn(array $columns, string $relation, mixed $categoryData=null){
        if($relation == 'children'){
            if(!empty($categoryData)){
                foreach ($categoryData as $category){
                    self::$data->add($category->only($columns));
                    self::filterNestedColumn($columns, $relation, $category[$relation]);
                }
            }
        }else{
            if(!is_null($categoryData)){
                self::$data->add($categoryData->only($columns));
                self::filterNestedColumn($columns, $relation, $categoryData[$relation]);
            }
        }
    }

    public function __call(string $method, array|null $parameters=null)
    {
        return $parameters ? self::$query->$method($parameters) : self::$query->$method();
    }

}
