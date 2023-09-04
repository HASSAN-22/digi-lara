<?php

namespace App\Http\Controllers\Related;

use App\Enums\IsOriginalEnum;
use App\Enums\PublishEnum;
use App\Enums\StatusEnum;
use App\Enums\UserAccessEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Guarantee;
use App\Models\Product;
use App\Models\Productcolor;
use App\Models\Productimage;
use App\Models\Productpricehistory;
use App\Models\Productproperty;
use App\Models\Productsize;
use App\Models\Productspecification;
use App\Models\Property;
use App\Models\Size;
use App\Models\User;
use App\Services\CategoryService;
use App\Services\NotificationService;
use App\Services\ProductService;
use App\Services\Uploader\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    private string $DIRECTORY = 'uploader/product';
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Product::latest()->search()->with(['user'=>fn($q)=>$q->select('id','name')]);
        $notifications = NotificationService::name('ProductNotification')->forUser((int)$user->id)->getUnRead();
        if($user->isAdmin()){
            $this->authorize('viewAny',Product::class);
        }else{
            $query = $query->where('user_id',$user->id);
            $this->authorize('viewAnySeller',Product::class);
        }
        $products = $query->paginate(10);
        $users = User::where('access',UserAccessEnum::SELLER->value)->get();
        $categories = Category::where('status',StatusEnum::ACTIVE->value)->with('children')->get();
        $brands = Brand::where('status',StatusEnum::ACTIVE->value)->get();
        $guarantees = Guarantee::get();
        $colors = Color::get();
        $sizes = Size::get();
        return ProductResource::collection($products)->additional([
            'users'=>$users,
            'brands'=>$brands,
            'sizes'=>$sizes,
            'colors'=>$colors,
            'categories'=>$categories,
            'guarantees'=>$guarantees,
            'notifications'=>$notifications,
            'amazing_offer_day'=>config('app.amazing_offer_days')
        ]);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'create' : 'createSeller',Product::class);
        DB::beginTransaction();
        try{

            // Upload original product image
            $path = $this->uploadMainImage($request);
            $productData = $this->productData($user, $request, $path);

            if($request->amazing_offer_percent > 0 and $request->is_amazing_offer == 'yes'){
                $productData['amazing_offer_status']= $user->isAdmin() ? StatusEnum::ACTIVE : StatusEnum::PENDING;
                $productData['amazing_offer_expire']= $user->isAdmin() ? now()->addHours(24)->timestamp : null;
            }
            // Insert product
            $product = Product::create($productData);

            // Upload other product image
            if($request->hasFile('images')){
                $this->insertImages($request, (int) $product->id);
            }

            if(count($request->specification_names) > 0){
                $this->insertSpecifications($request, (int) $product->id);
            }

            if(count($request->color_ids) > 0){
                $this->insertColors($request, (int) $product->id);
            }

            if(count($request->size_ids) > 0){
                $this->insertSizes($request, (int) $product->id);
            }

            if(count($request->property_ids) > 0){
                $this->insertProperties($request, (int) $product->id);
            }

            if($user->isSeller()){
                NotificationService::name('ProductNotification')->send((int)$product->id,null,true,false);
            }

            Productpricehistory::create(['product_id'=>$product->id,'price'=>$product->price]);

            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>$e->getMessage()],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'view' : 'viewSeller',$product);
        $product = $product->where('id',$product->id)->with([
            'productImages',
            'productColors',
            'productSizes',
            'productProperties'=>fn($q)=>$q->with('propertyType'),
            'productSpecifications',
            'category'=>fn($q)=>$q->select('id','commission')
        ])->first();
        try{
            NotificationService::name('ProductNotification')->delete((int)$product->id, 'data->product->id');
        }catch (\Exception $e){}
        return response(['status'=>'success','data'=>$product]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'update' : 'updateSeller',$product);
        DB::beginTransaction();
        try{
            $notification = NotificationService::name('ProductNotification');

            // Upload original product image and remove previous image
            $path = $product->image;
            if($request->hasFile('image')){
                $path = $this->uploadMainImage($request);
                $this->removeOriginalImage($product->image);
            }
            // Update product
            $productData = $this->productData($user, $request,$path);
            $emptyAmazingOffer = function ()use(&$productData){
                $productData['amazing_offer_status'] = null;
                $productData['amazing_offer_expire'] = null;
                $productData['amazing_offer_percent'] = null;
            };

            if($user->isSeller()){
                if($request->amazing_offer_percent > 0 and $request->is_amazing_offer == 'yes' and $product->amazing_offer_status !== 'yes'){
                    $productData['amazing_offer_status']= StatusEnum::PENDING;
                }elseif($product->amazing_offer_status == 'yes' and $request->is_amazing_offer == 'yes'){
                    $productData['amazing_offer_percent'] = $product->amazing_offer_percent;
                }else{
                    $emptyAmazingOffer();
                }
                $notification->send((int)$product->id,null,true,false,'data->product->id');
            }elseif($user->isAdmin()){
                if($request->is_amazing_offer == 'no' or $request->amazing_offer_percent <= 0){
                    $emptyAmazingOffer();
                }elseif($request->amazing_offer_update){
                    $productData['amazing_offer_status'] = StatusEnum::ACTIVE;
                    $productData['amazing_offer_expire'] = now()->addHours(24)->timestamp;
                }
                $notification->send((int)$product->id,$product->user,false,true, 'data->product->id');
            }
            $product->update($productData);

            // Upload other product images
            if($request->hasFile('images')){
                $this->insertImages($request, (int) $product->id);
            }

            if(count($request->specification_names) > 0){
                $product->productSpecifications()->delete();
                $this->insertSpecifications($request, (int) $product->id);
            }

            if(count($request->color_ids) > 0){
                $product->productColors()->delete();
                $this->insertColors($request, (int) $product->id);
            }

            if(count($request->size_ids) > 0){
                $product->productSizes()->delete();
                $this->insertSizes($request, (int) $product->id);
            }

            if(count($request->property_ids) > 0){
                $product->productProperties()->delete();
                $this->insertProperties($request, (int) $product->id);
            }

            if($request->price != $product->price){
                Productpricehistory::create(['product_id'=>$product->id,'price'=>$product->price]);
            }


            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>$e->getMessage()],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'view' : 'viewSeller',$product);
        $images = $product->productImages;
        if($product->delete()){
            try{
                $this->removeOriginalImage($product->image);
                $this->removeImages($images);
                NotificationService::name('ProductNotification')->delete((int)$product->id, 'data->product->id');
            }catch (\Exception $e){};
            return response(['status'=>'success']);
        }
        return response(['status'=>'error'],500);

    }

    /**
     * Check if the seller can add the product to the amazing offer
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function checkAmazingOffer(){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'create' : 'createSeller',Product::class);
        $addAmazing = true;
        if($user->isSeller()){
            $addAmazing = $user->products()->whereNotNull('amazing_offer_percent')->whereDate('amazing_offer_expire','<',now()->subDays((int)config('app.amazing_offer_days'))->timestamp)->get()->count() <= 0;
        }
        return response(['status'=>'success','add_amazing'=>$addAmazing]);
    }

    /**
     * Get all product properties
     * @param Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getProperties(Category $category){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'viewAny' : 'viewAnySeller',Product::class);
        $properties = call_user_func_array('array_merge',CategoryService::category()->active()->findById($category->id)->properties(true)->get()
            ->map(fn($item)=>$item->categoryProperties)->toArray());
        return response(['status'=>'success','data'=>$properties]);
    }

    /**
     * Get all product property types
     * @param Property $property
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getPropertyTypes(Property $property){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'viewAny' : 'viewAnySeller',Product::class);
        return response(['status'=>'success','data'=>$property->propertyTypes]);
    }

    /**
     * Remove singular image
     * @param Productimage $productimage
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function destroyImage(Productimage $productimage){
        if($productimage->delete()){
            removeFile($productimage->image);
            return response(['status'=>'success']);
        }
        return response(['status'=>'error'],500);
    }

    /**
     * Create additional images
     * @param ProductRequest $request
     * @param int $productId
     * @return int|string
     */
    private function insertImages(ProductRequest $request, int $productId): string|int
    {
        $images = [];
        foreach ($request->images as $key => $image) {
            $path = Upload::resizable(config('app.large_image_size'), config('app.large_image_size'),'webp')->upload($request, 'images', $this->DIRECTORY, null, $key);
            $images[] = [
                'product_id' => $productId,
                'image' => $path,
            ];
        }
        return Productimage::insert($images);
    }

    /**
     * Definition of specific specifications by the seller
     * @param ProductRequest $request
     * @param int $productId
     * @return void
     */
    private function insertSpecifications(ProductRequest $request, int $productId): void
    {
        $specifications = [];
        foreach ($request->specification_names as $key => $name) {
            $specifications[] = [
                'product_id' => $productId,
                'name' => $name,
                'description' => $request->specification_description[$key]
            ];
        }
        Productspecification::insert($specifications);
    }

    /**
     * Colors that can be priced
     * @param ProductRequest $request
     * @param int $productId
     * @return void
     */
    private function insertColors(ProductRequest $request, int $productId): void
    {
        $colors = [];
        foreach ($request->color_ids as $key => $color) {
            $colors[] = [
                'product_id' => $productId,
                'color_id' => $color,
                'price' => $request->colors_price[$key] ?? 0,
            ];
        }
        Productcolor::insert($colors);
    }

    /**
     * Sizes that can be priced
     * @param ProductRequest $request
     * @param int $productId
     * @return void
     */
    private function insertSizes(ProductRequest $request, int $productId): void
    {
        $sizes = [];
        foreach ($request->size_ids as $key => $size) {
            $sizes[] = [
                'product_id' => $productId,
                'size_id' => $size,
                'price' => $request->sizes_price[$key] ?? 0,
            ];
        }
        Productsize::insert($sizes);
    }

    /**
     * Common features to use in filtering and guiding the buyer to purchase
     * @param ProductRequest $request
     * @param int $productId
     * @return void
     */
    private function insertProperties(ProductRequest $request, int $productId): void
    {
        $properties = [];
        foreach ($request->property_ids as $key => $propertyId) {
            foreach ($request->property_types[$key] as $k=>$property_type){
                $properties[] = [
                    'product_id' => $productId,
                    'property_id' => $propertyId,
                    'propertytype_id' => $property_type,
                ];
            }
        }
        Productproperty::insert($properties);
    }

    /**
     * Set product data for inserting or updating
     * @param $user
     * @param ProductRequest $request
     * @param string $imagePath
     * @return array
     */
    private function productData($user, ProductRequest $request, string $imagePath): array
    {
        $data = [
            'user_id' => $user->isAdmin() ? $request->seller_id : $user->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'guarantee_id' => $request->guarantee_id,
            'ir_name' => $request->ir_name,
            'slug' => slug($request->ir_name),
            'en_name' => $request->en_name,
            'sku' => $request->sku,
            'amazing_offer_percent' => $request->amazing_offer_percent,
            'price' => $request->price,
            'guarantee_month' => $request->guarantee_month,
            'amazing_price' => $request->amazing_price,
            'packing_length' => $request->packing_length,
            'packing_width' => $request->packing_width,
            'packing_height' => $request->packing_height,
            'packing_weight' => $request->packing_weight,
            'physical_length' => $request->physical_length,
            'physical_width' => $request->physical_width,
            'physical_height' => $request->physical_height,
            'physical_weight' => $request->physical_weight,
            'publish' => $user->isAdmin() ? PublishEnum::from($request->publish) : PublishEnum::DRAFT,
            'count' => $request->count,
            'image' => $imagePath,
            'is_original' => IsOriginalEnum::from($request->is_original),
            'strengths' => json_encode($request->strengths),
            'weak_points' => json_encode($request->weak_points),
            'description' => $request->description,
            'more_description' => $request->more_description,
        ];

//        if($user->isAdmin()){
//            if(!is_null($request->amazing_offer_percent) and $request->is_amazing_offer == 'yes'){
//                $data['amazing_offer_status']= StatusEnum::ACTIVE;
//                $data['amazing_offer_expire']= now()->addHours(24)->timestamp;
//            }
//        }else{
//            $data['amazing_offer_status']= StatusEnum::PENDING;
//            $data['amazing_offer_expire']= null;
//        }


        return $data;
    }

    /**
     * Remove product images
     * @param mixed $images
     * @return void
     */
    private function removeImages(mixed $images): void
    {
        foreach ($images as $key=>$image){
            if($key == 0){

            }
            removeFile($image->image);
        }

    }

    /**
     * Delete the original image along with all other sizes
     * @param $image
     * @param $id
     * @return void
     */
    private function removeOriginalImage($image){
        $largeSize = config('app.large_image_size');
        $mediumSize = config('app.medium_image_size');
        $smallSize = config('app.small_image_size');

        $name = str_replace('/storage/product/'.$largeSize.'x'.$largeSize.'_','',$image);
        $medium = "storage/product/{$mediumSize}x{$mediumSize}_{$name}";
        $small = "storage/product/{$smallSize}x{$smallSize}_{$name}";

        removeFile($image);
        removeFile($medium);
        removeFile($small);
    }

    /**
     * Upload original image for three size Large, Medium, Small
     * @param ProductRequest $request
     * @return string|null
     */
    private function uploadMainImage(ProductRequest $request): ?string
    {
        $largeSize = config('app.large_image_size');
        $mediumSize = config('app.medium_image_size');
        $smallSize = config('app.small_image_size');

        $path = Upload::resizable($largeSize, $largeSize,'webp')->upload($request, 'image', $this->DIRECTORY);
        $name = str_replace('/storage/product/'.$largeSize.'x'.$largeSize.'_','',$path);
        Upload::resizable($mediumSize, $mediumSize,'webp', $name)->upload($request, 'image', $this->DIRECTORY);
        Upload::resizable($smallSize, $smallSize,'webp', $name)->upload($request, 'image', $this->DIRECTORY);
        return $path;
    }

}
