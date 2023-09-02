<?php

namespace App\Http\Controllers;

use App\Enums\AvailableEnum;
use App\Enums\CategoryTypeEnum;
use App\Enums\PublishEnum;
use App\Enums\ShippingEnum;
use App\Enums\SmsTypeEnum;
use App\Enums\StatusEnum;
use App\Http\Requests\ContactRequest;
use App\Models\Address;
use App\Models\Amazingalert;
use App\Models\Basket;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Compare;
use App\Models\Contact;
use App\Models\Couponuser;
use App\Models\News;
use App\Models\Newsletter;
use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Page;
use App\Models\Productcomment;
use App\Models\Productnotifyexists;
use App\Models\Productquestion;
use App\Models\Productquestionanswer;
use App\Models\Province;
use App\Models\Shopconfig;
use App\Models\Size;
use App\Models\Transport;
use App\Models\User;
use App\Models\Wishlist;
use App\Models\Product;
use App\Models\Slider;
use App\Models\Widget;
use App\Services\BasketService;
use App\Services\CategoryService;
use App\Services\CommentService;
use App\Services\CouponService;
use App\Services\NotificationService;
use App\Services\ProductService;
use App\Services\SMS\SMS;
use App\Services\SMS\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{

    /**
     * Display data for the body of the main page
     * @return Response
     */
    public function content(){
        $sliders = Slider::get();
        $widgets = Widget::where('page','root')->first();
        $widgets = $this->getWidgets($widgets);
        $bestSellingProducts = ProductService::product()->published()->activeSeller()->bestsellings()->get('id','ir_name','image','slug');
        $half = ceil($bestSellingProducts->count() / 3);
        $bestSellingProducts = $bestSellingProducts->chunk($half);
        $news = News::where('publish',PublishEnum::PUBLISHED)->latest()->get(['id','title','image','slug'])->take(4);
        return response(['status'=>'success','data'=>['sliders'=>$sliders,'widgets'=>$widgets,'best_selling_products'=>$bestSellingProducts,'news'=>$news]]);
    }

    /**
     * Display data for site
     * @return Response
     */
    public function index(){
        $pages = Page::whereBetween('id',[1,9])->get();
        return response(['status'=>'success','data'=>[
            ...getShopConfig(),
            'pages'=>$pages
        ]]);
    }


    /**
     * Display data for the home page header
     * @return Response
     */
    public function indexHeader(){
        $categories = CategoryService::category()->active()->withRelations(['children'])->where(['type',CategoryTypeEnum::CATEGORY])->get();
        $newsCategories = CategoryService::category()->active()->where(['type',CategoryTypeEnum::NEWS])->where(['parent_id','0'])->withRelations(['children'])->get();
        return response(['status'=>'success','data'=>['categories'=>$categories,'news_categories'=>$newsCategories]]);
    }

    /**
     * Get user addresses
     * @return Response
     */
    public function getAddresses(){
        $user = auth()->user();
        $addresses = $user->addresses;
        $provinces = Province::get();
        return response(['status'=>'success','data'=>['addresses'=>$addresses, 'provinces'=>$provinces]]);
    }

    /**
     * Get basket extra data
     * @return Response
     */
    public function getBasketExtraData(){
        $wallet = auth()->user()->wallet;
        $amount = !is_null($wallet) ? $wallet->amount : 0;
        $transports = Transport::get();
        return response(['status'=>'success','data'=>['amount'=>$amount,'transports'=>$transports]]);
    }

    public function checkCoupon(Request $request){
        $result = CouponService::coupon($request->code)->isValid();
        return response(['status'=>'success','data'=>$result]);
    }

    /**
     * Choose address for getting order
     * @param Address $address
     * @return Response
     */
    public function chooseAddress(Request $request, Address $address){
        $user = auth()->user();
        DB::beginTransaction();
        try{
            $user->addresses()->update(['is_selected'=>'no']);
            $address->update(['is_selected'=>'yes']);
            if($request->exists('save_to_order') and $request->save_to_order == 'yes'){
                Order::where('id',$request->order_id)->update([
                    'address'=>json_encode([
                        'receiver'=>$address->receiver_name . ' ' . $address->receiver_last_name,
                        'address'=>$address->address,
                        'mobile'=>$address->mobile,
                        'address_id'=>$address->id,
                        'postal_code'=>$address->postal_code,
                        'unit'=>$address->unit,
                        'plaque'=>$address->plaque,
                        'province_id'=>$address->province->name,
                        'city_id'=>$address->city->name,
                    ])
                ]);
            }
            DB::commit();
            return response(['status'=>'success']);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }

    }

    /**
     * Get cities by province
     * @param Province $province
     * @return Response
     */
    public function getCities(Province $province){
        return response(['status'=>'success','data'=>$province->cities]);
    }

    /**
     * Get user basket count
     * @return Response
     */
    public function basketCount(){
        $cartCount = auth()->user()->baskets()->sum('count');
        return response(['status'=>'success','data'=>$cartCount]);
    }
    /**
     * Get user basket
     * @return Response
     */
    public function getBasket(Request $request){
        $withCoupon = json_decode($request->with_coupon);
        $withProduct = json_decode($request->with_product);
        $withCount = json_decode($request->with_count);
        $productColumns = ['guarantee_id','brand_id','category_id','physical_weight','image','ir_name','amazing_offer_percent','amazing_offer_status','count','amazing_offer_expire','amazing_price','price','id'];
        $query = BasketService::setProductColumns($productColumns)
            ->withCoupon($withCoupon);
        if($withProduct){
            $query->productWith([
                'guarantee'=>fn($q)=>$q->select('id','guarantee'),
                'brand'=>fn($q)=>$q->select('id','name'),
                'category'=>fn($q)=>$q->select('id','weight_type')
            ]);
        }
        $query = $query->baskets(auth()->user())
            ->removeFinishedItems();
        $result = $withCount ? $query->calcAmount()->get() : $query->get();
        return response(['status'=>'success','data'=>$result]);
    }

    /**
     * Add user orders
     * @return Response
     */
    public function addOrder(Request $request){
        $user = auth()->user();
        $productColumns = ['ir_name','image','user_id','category_id','physical_weight','amazing_offer_percent','amazing_offer_status','count','amazing_offer_expire','amazing_price','price','id','category_id'];
        $result = BasketService::setProductColumns($productColumns)
            ->withCoupon(true)->productWith(['category'=>fn($q)=>$q->select('id','commission','weight_type')])->baskets($user)
            ->removeFinishedItems()->get();

        DB::beginTransaction();
        try {
            $weightType = $result['baskets']->unique('product.category.weight_type')->pluck('product.category.weight_type')->toArray();
            $transportAmount = Transport::whereIn('weight_type',$weightType)->get()->sum('fixed_price');
            $wallet = $user->wallet;
            $address = $user->addresses()->where('addresses','yes')->with(['province','city'])->first();
            $order = Order::create([
                'user_id'=>$user->id,
                'shipping_status'=>ShippingEnum::PAYMENT_PENDING,
                'transport_cost'=>$transportAmount,
                'reduced_wallet'=>0,
                'address'=>json_encode([
                    'receiver'=>$address->receiver_name . ' ' . $address->receiver_last_name,
                    'address'=>$address->address,
                    'mobile'=>$address->mobile,
                    'address_id'=>$address->id,
                    'postal_code'=>$address->postal_code,
                    'unit'=>$address->unit,
                    'plaque'=>$address->plaque,
                    'province_id'=>$address->province->name,
                    'city_id'=>$address->city->name,
                ])
            ]);
            $orderDetails = [];
            $couponUserIds = [];
            $fullAmount = 0;
            foreach($result['baskets'] as $basket){
                $product = $basket->product;
                $discount = BasketService::getDiscount($basket);
                $property = BasketService::getPropertyAmount($basket);
                $couponUserIds[] = $discount['couponIds'];
                $fullAmount += $discount['amount'];
                $orderDetails[] = [
                    'user_id'=>$product->user_id,
                    'order_id'=>$order->id,
                    'product_id'=>$product->id,
                    'brand'=>$product->brand->name,
                    'name'=>$product->ir_name,
                    'sku'=>$product->sku,
                    'price'=>$product->price,
                    'image'=>$product->image,
                    'count'=>$basket->count,
                    'discount'=>$discount['discount'],
                    'discount_type'=>$discount['type'],
                    'property_type'=>$property['type'],
                    'property_name'=>$property['name'],
                    'property_price'=>$property['amount'],
                    'property_color'=>$property['colorCode'],
                    'amount'=>$discount['amount'],
                    'commission'=>$product->category->commission,
                    'shipping_status'=>ShippingEnum::PAYMENT_PENDING,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ];
            }
            Orderdetail::insert($orderDetails);
            $couponUserIds = call_user_func_array('array_merge', $couponUserIds);
            if(count($couponUserIds) > 0){
                Couponuser::whereIn('coupon_id',$couponUserIds)->where('user_id',$user->id)->update(['is_available'=>AvailableEnum::NOT_AVAILABLE]);
            }
            $fullAmount += $transportAmount;
            $user->baskets()->delete();
            if($request->use_wallet and $wallet->amount > 0){
                $result = $wallet->amount - $fullAmount;
                $order->update(['reduced_wallet'=>$result > 0 ? $result : $wallet->amount]);
                $wallet->update(['amount'=> max($result, 0)]);
            }
            $fullAmount = $fullAmount - $order->reduced_wallet;
            if($fullAmount <= 0){
                $order->update(['shipping_status'=>ShippingEnum::REVIEW_QUEUE]);
                $order->orderDetails()->update(['shipping_status'=>ShippingEnum::REVIEW_QUEUE]);
            }
            DB::commit();
            if($fullAmount > 0){
                // to bank
            }else{
                return response(['status'=>'success'],201);
            }
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>[$e->getMessage(),$e->getLine(), $e->getTrace()]],500);
        }
    }

    /**
     * Remove basket item
     * @param Basket $basket
     * @return Response
     */
    public function removeBasket(Basket $basket){
        if($basket->delete()){
            $count = $basket->user->baskets()->sum('count');
            return response(['status'=>'success','data'=>$count]);
        }
        return response(['stastus'=>'error'],500);
    }


    /**
     * Add a product to basket
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function addBasket(Request $request, Product $product){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $baskets = $user->baskets();

            if($product->count <= 0 or $product->count <= $baskets->where('product_id',$product->id)->sum('count')){
                return response(['status'=>'error','data'=>'product_count_not_enough'],500);
            }
            if(!empty($request->size)){
                $condition = ['product_id'=>$product->id,'productsize_id'=>$request->size];
            }else{
                $condition = ['product_id'=>$product->id,'productcolor_id'=>$request->color];
            }
            $baskets->updateOrCreate(
                $condition,
                [
                    'user_id'=>$user->id,
                    'count'=>DB::raw('count + 1')
                ]
            );

            DB::commit();
            return response(['status'=>'success','data'=>$user->baskets()->sum('count')]);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>[$e->getMessage()]],500);
        }
    }/**
     * Removes a product from the basket
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function minusBasket(Request $request, Product $product){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $baskets = $user->baskets();

            if($baskets->where('product_id',$product->id)->sum('count') <= 1){
                return response(['status'=>'error','data'=>'product_minimum'],500);
            }
            if(!empty($request->size)){
                $condition = ['product_id'=>$product->id,'productsize_id'=>$request->size];
            }else{
                $condition = ['product_id'=>$product->id,'productcolor_id'=>$request->color];
            }
            $baskets->updateOrCreate(
                $condition,
                [
                    'user_id'=>$user->id,
                    'count'=>DB::raw('count - 1')
                ]
            );

            DB::commit();
            return response(['status'=>'success','data'=>$user->baskets()->sum('count')]);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error','e'=>[$e->getMessage()]],500);
        }
    }

    /**
     * Search on products by name
     * @param Request $request
     * @return Response
     */
    public function searchProduct(Request $request){
        $products = ProductService::product()->published()->activeSeller()->where(['ir_name','like',"%{$request->search}%"])->get(['id','ir_name','slug','price','slug','image']);
        return response(['status'=>'success','data'=>$products]);
    }

    /**
     * Get all product for current category and parents
     * @return Response
     */
    public function productList(Request $request){
        $slug = $request->slug;
        $categoryIds = CategoryService::category()->active()->findBySlug($slug)->childrens('id')->pluck('id')->toArray();
        $category = CategoryService::category()->active()->findBySlug($slug)->first();
        $query = ProductService::product()->published()->withRelations(['productComments'])->activeSeller()->byCategory($categoryIds);
        $sort = $request->sort;
        if(!is_null($sort)){
            if($sort === 'top_visit'){
                $query = $query->topVisit();
            }else if($sort === 'newest'){
                $query = $query->newest();
            }else if($sort === 'bestselling'){
                $query = $query->bestsellings();
            }else if($sort === 'cheapest'){
                $query = $query->cheapest();
            }else if($sort === 'expensive'){
                $query = $query->expensive();
            }

        }

        if($request->exists('color')){
            $query = $query->filterByColor($request->color);
        }elseif($request->exists('size')){
            $query = $query->filterBySize($request->size);
        }elseif($request->exists('property')){
            $query = $query->FilterByProperties($request->property);
        }elseif($request->exists('only_available_products')){
            $query = $query->available($request->only_available_products);
        }elseif($request->exists(['from_price','to_price'])){
            $query = $query->filterByPrice($request->from_price, $request->to_price);
        }

        $products = $query->get('id','image','ir_name','count','price','amazing_price','amazing_offer_percent','slug','amazing_offer_status','amazing_offer_expire')
            ->map(function($item){
               $item['rating'] = $this->calcProductRating($item->productComments)[1];
               return $item;
            })->toArray();
        $products = paginate($request, $products);
        return response(['status'=>'success','data'=>['products'=>$products, 'category'=>$category]]);
    }

    /**
     * Get product properties
     * @param string $slug
     * @return Response
     */
    public function getProductProperties(string $slug){
        $colors = Color::get();
        $sizes = Size::get();
        $properties = CategoryService::category()->active()->findBySlug($slug)->properties(true,true)->get()
            ->map(fn($item)=>$item->categoryProperties);
        return response(['status'=>'success','data'=>['properties'=>$properties,'colors'=>$colors,'sizes'=>$sizes]]);
    }


    /**
     * Get product detail
     * @param string $slug
     * @return Response
     */
    public function getProductDetail(Request $request,string $slug){
        $productRelations = ['productSpecifications',
        'productProperties'=>fn($q)=>$q->with(['property','propertyType']),
        'productColors'=>fn($q)=>$q->with('color'),
        'productSizes'=>fn($q)=>$q->with('size'),
        'productImages',
        'guarantee'=>fn($q)=>$q->select('id','guarantee'),
        'user'=>fn($q)=>$q->select('id','name','avatar'),
        'brand'=>fn($q)=>$q->select('id','name','en_name','logo'),
        'category'=>fn($q)=>$q->select('id','title','parent_id','slug','weight_type'),
        'productComments'=>fn($q)=>$q->where('status',StatusEnum::ACTIVE),
        'productCommentImages'=>fn($q)=>$q->whereRelation('productComment','status',StatusEnum::ACTIVE),
        'productPriceHistories'];
        $product = ProductService::product()->published()->withRelations($productRelations)->findBySlug($slug)->first();
        $category = $product->category;
        $relatedProducts = ProductService::product()->published()->activeSeller()->findBy('category_id',$category->id)->get();
        $transport = Transport::where('weight_type',$category->weight_type)->first();
        $product['transport_cost'] = !is_null($transport->fixed_price) ? number_format($transport->fixed_price) . ' ریال ' : $transport->name;
        $ip = $request->ip();
        $product->visits()->where('ip',$ip)->updateOrCreate([
           'ip'=>$ip
        ]);
        list($product['comment_count'], $product['rating'], $product['suggestion']) = $this->calculateRatingAndSuggestion($product);
        $categoryParents = CategoryService::category()->active()->findById($category->id)->parents('id','parent_id','title','slug')->sortBy('id')->values();
        return response(['status'=>'success','data'=>[
            'product'=>$product,
            'related_products'=>$relatedProducts,
            'category'=>$category,
            'category_parents'=>$categoryParents,
        ]]);
    }

    /**
     * Receive a series of user information related to the product
     * @param Product $product
     * @return Response
     */
    public function getUserData(Product $product){
        $user = auth()->user();
        $hasWishlist = $user->wishlists()->where('product_id',$product->id)->first() != null;
        $hasAmazingAlert = $user->amazingAlerts()->where('product_id',$product->id)->first() != null;
        $hasProductNotifyExists = $user->productNotifyExists()->where('product_id',$product->id)->first() != null;
        return response(['status'=>'success','data'=>[
            'has_wishlist'=>$hasWishlist,
            'has_amazing_alert'=>$hasAmazingAlert,
            'has_product_notify_exists'=>$hasProductNotifyExists,
        ]]);
    }

    /**
     * Add or remove product to wishlist
     * @param Product $product
     * @return Response
     */
    public function toggleWishlist(Product $product){
        $user = auth()->user();
        $added = false;
        $withlist = $user->wishlists()->where('product_id',$product->id)->first();
        if(is_null($withlist)){
            Wishlist::create(['user_id'=>$user->id,'product_id'=>$product->id]);
            $added = true;
        }else{
            $withlist->delete();
        }
        return response(['status'=>'success','data'=>$added]);
    }

    /**
     * Notifies the user when the product is amazing
     * @param Product $product
     * @return Response
     */
    public function toggleAmazingAlert(Product $product){
        $user = auth()->user();
        $added = false;
        $alert = $user->amazingAlerts()->where('product_id',$product->id)->first();
        if(is_null($alert)){
            Amazingalert::create(['user_id'=>$user->id,'product_id'=>$product->id]);
            $added = true;
        }else{
            $alert->delete();
        }
        return response(['status'=>'success','data'=>$added]);
    }

    /**
     * Add product to compare list
     * @param Product $product
     * @return Response
     */
    public function addCompare(Request $request,Product $product){
        $user = auth()->user();
        if($request->exists('compare') and $request->compare == 'detail'){
            $user->compares()->delete();
        }
        Compare::create(['user_id'=>$user->id,'product_id'=>$product->id]);
        return response(['status'=>'success']);
    }

    /**
     * Remove the product from the comparison list
     * @param Product $product
     * @return Response
     */
    public function removeCompare(Request $request,Product $product){
        return auth()->user()->compares()->where("product_id",$product->id)->delete() ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * @return Response
     */
    public function getCompares(){
        $user = auth()->user();
        $columns = ['id','image','ir_name','category_id','count','price','amazing_price','amazing_offer_percent','slug','amazing_offer_status','amazing_offer_expire'];
        $compares = $user->compares()->with([
            'product'=>fn($q)=>$q->with(['productProperties'=>fn($q)=>$q->with(['property','propertyType']),'productComments'])->select($columns),
        ])->get()->map(function ($item){
            list($average, $rating) = $this->calcProductRating($item->product->productComments);
            $item->product['rating'] = $rating;
            return $item->product;
        });
        $products = $compares[0]->category->products()->whereNotIn('id',$compares->pluck('id')->toArray())->where('publish',PublishEnum::PUBLISHED->value)->whereHas('user',fn($q)=>$q->where('status','yes'))->get($columns);
        return response(['status'=>'success','data'=>['compares'=>$compares, 'products'=>$products]]);
    }

    /**
     * Receive product availability notification
     * @param Product $product
     * @return Response
     */
    public function toggleProductNotifyExist(Product $product){
        $userId = auth()->Id();
        $notify = $product->productNotifyExists()->where('user_id',$userId)->first();
        $added = false;
        if($notify){
            $notify->delete();
        }else{
            $added = true;
            Productnotifyexists::create(['user_id'=>$userId, 'product_id'=>$product->id]);
        }

        return response(['status'=>'success','data'=>$added]);
    }

    /**
     * get product questions
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function getProductQuestions(Request $request, Product $product){
        $questions = $product->productQuestions()->where('status',StatusEnum::ACTIVE)
            ->with(['productQuestionAnswers'=>fn($q)=>$q->with(['user'=>fn($q)=>$q->select('id','name'),'likes'])])
            ->orderBy('created_at',$request->sort)->paginate(10);
        return response(['status'=>'success','data'=>$questions]);
    }

    /**
     * Add a question for product
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function addProductQuestion(Request $request, Product $product){
        $request->validate([
            'question'=>['required','string','max:500']
        ]);
        $question = Productquestion::create([
           'user_id'=>auth()->Id(),
           'product_id'=>$product->id,
           'question'=>$request->question,
           'status'=>StatusEnum::PENDING,
        ]);
        try{
            NotificationService::name('ProductQuestionNotification')->send($question->id,null, true, false);
            NotificationService::name('ProductQuestionNotification')->send($question->id,$product->user, false);
        }catch (\Exception $e){}
        return $question ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }

    /**
     * Answer to product question
     * @param Request $request
     * @param Productquestion $productquestion
     * @return Response
     */
    public function addProductQuestionAnswer(Request $request, Productquestion $productquestion){
        $request->validate([
            'answer'=>['required','string','max:500']
        ]);
        $answer = Productquestionanswer::create([
            'user_id'=>auth()->Id(),
            'productquestion_id'=>$productquestion->id,
            'answer'=>$request->answer,
            'status'=>StatusEnum::PENDING,
        ]);
        return $answer ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }

    /**
     * Insert a comment for product
     * @param Request $request
     * @param Product $product
     * @return Response
     */
    public function addComment(Request $request, Product $product){

        return CommentService::store($request, (int) $product->id) ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }

    /**
     * Get product comments
     * @param Product $product
     * @param Request $request
     * @return Response
     */
    public function getProductComments(Request $request, Product $product){
        $productComments = $product->productComments()->where('status',StatusEnum::ACTIVE);
        $comments = $productComments->with(['productCommentImages','likes','user'=>fn($q)=>$q->select('id','name')])
            ->orderBy('created_at',$request->sort)->paginate(10);

        $purchased = false;
        return response(['status'=>'success','data'=>[
            'purchased'=>$purchased,
            'comments'=>$comments
        ]]);
    }

    /**
     * Like a post
     * @param Request $request
     * @return Response
     */
    public function likeOrDislike(Request $request){
        $userId = auth()->Id();
        $type = new ('\App\Models\\'.$request->type)();
        $post = $type->find($request->post_id);
        $like = $post->likes()->where('user_id',$userId)->first();
        if($like){
            if($like->is_like == $request->is_like){
                $like->delete();
            }else{
                $like->update(['is_like'=>$request->is_like]);
            }
        }else{
            $post->likes()->where('user_id',$userId)->create(['user_id'=>$userId,'is_like'=>$request->is_like]);
        }
        return response(['status'=>'success']);
    }

    /**
     * Store a report
     * @param Request $request
     * @return Response
     */
    public function sendReport(Request $request){
        $request->validate(['report_description'=>['required','string','max:5000']]);
        $post = (new ("\App\Models\\".$request->type))->find($request->post_id);
        $report = $post->reports()->create(['user_id'=>auth()->Id(), 'description'=>$request->report_description]);
        return $report ? response(['status'=>'success']) : response(['status'=>'error']);
    }

    /**
     * Get a comment
     * @param Productcomment $productcomment
     * @return Response
     */
    public function getComment(Productcomment $productcomment){
        $comment = $productcomment->where('id',$productcomment->id)->with(['productCommentImages','user'=>fn($q)=>$q->select('id','name')])->first();
        return response(['status'=>'success','data'=>$comment]);
    }

    /**
     * Checking the validity of the user token
     * @return Response
     */
    public function checkToken(){
        if(auth()->check()){
            $user = auth()->user();
            $permissions = $user->permissions->pluck('name');
            return response(['status'=>'success','data'=>[
                'token_valid'=>$this->tokenIsExpire($user),
                'access'=>$user->access,
                'name'=>$user->name,
                'id'=>$user->id,
                'avatar'=>$user->avatar,
                'status'=>$user->status,
                'permissions'=>$permissions]
            ],200);
        }
        return response(['status'=>'error'],500);

    }

    /**
     * Send sms code
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws \Exception
     */
    public function sendCode(Request $request){
        $request->validate(['mobile'=>['required','numeric','digits:11,11']]);
        $user = !is_null(User::where('mobile',$request->mobile)->first());
        if(!$user || ($request->exists('force_send') and $request->force_send == 'yes')){
//            SmsService::send($request->mobile, $request->pattern, 'name');
            SMS::driver(config('app.sms_driver'))->sendMessage([$request->mobile], SmsService::generateCode(), SmsTypeEnum::CODE);
        }
        return response(['status'=>'success', 'user_exists'=>$user],200);
    }

    /**
     * Resend sms code
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws \Exception
     */
    public function reSendCode(Request $request){
        $request->validate(['mobile'=>['required','numeric','digits:11,11']]);
//        SmsService::send($request->mobile, $request->pattern, 'name');

        SMS::driver(config('app.sms_driver'))->sendMessage([$request->mobile], SmsService::generateCode(), SmsTypeEnum::CODE);
        return response(['status'=>'success'],200);
    }

    /**
     * Insert contact us
     * @param ContactRequest $request
     * @return Response
     */
    public function sendContact(ContactRequest $request){
        $contact = Contact::create([
            'subject'=>$request->subject,
            'username'=>$request->username,
            'email'=>$request->email,
            'mobile'=>$request->mobile,
            'message'=>$request->message,
        ]);
        return $contact ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function getFaqCategories(){
        $faqCategories = CategoryService::category()->where([['type'=>CategoryTypeEnum::FAQ]])->active()->get();
        return response(['status'=>'success','data'=>$faqCategories]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     */
    public function getFaqs(string $slug){
        $category = CategoryService::category()->active()->findBySlug($slug)->withRelations(['faqs'])->first();
        return response(['status'=>'success','data'=>$category]);
    }

    /**
     * Store visitor email
     * @param Request $request
     * @return Response
     */
    public function addNewsletter(Request $request){
        $request->validate(['email'=>['required','string','email','max:255','unique:newsletters,email,id']]);
        $newsletter = Newsletter::create(['email'=>$request->email]);
        return $newsletter ? response(['status'=>'success'],201) : response(['status'=>'error'],500);
    }

    /**
     * Get news by category
     * @param string|null $slug
     * @return Response
     */
    public function getNewsList(string|null $slug=null){
        $query = new News();
        $category = null;
        if($slug or $slug != ''){
            $category = Category::where('slug',$slug)->with(['news'])->first();
            $query = $category->news();
        }

        $news = $query->search()->where('publish',PublishEnum::PUBLISHED)->latest()->with(['user'=>fn($q)=>$q->select('id','name')])->paginate(10);
        return response(['status'=>'success','data'=>['news'=>$news,'category'=>$category]]);
    }

    /**
     * Get news by slug
     * @param string $slug
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getNewsDetail(string $slug){
        $search = trim(request()->get('search'));
        if($search != ''){
            $news = News::where('publish',PublishEnum::PUBLISHED)->search()->with(['user'=>fn($q)=>$q->select('id','name')])->first();
        }else{
            $news = News::where('slug',$slug)->with(['user'=>fn($q)=>$q->select('id','name')])->first();
        }
        $randomNews = News::where('publish',PublishEnum::PUBLISHED)->latest()->inRandomOrder()->get(['id','title','image','created_at','slug'])->take(10);
        $categoryTitle = $news->category->title;
        return response(['status'=>'success','data'=>['news'=>$news,'random_news'=>$randomNews,'category_title'=>$categoryTitle]]);
    }

    /**
     * Get best selling products
     * @return Response
     */
    public function bestSellings(Request $request){
        $bestSellingProducts = ProductService::product()->withRelations(['productComments'])->published()->activeSeller()->bestsellings()
            ->get()->map(function($item){
                $item['rating'] = $this->calcProductRating($item->productComments)[1];
                return $item->only('id','image','ir_name','count','price','amazing_price','amazing_offer_percent','slug','amazing_offer_status','amazing_offer_expire','rating');
            })->toArray();

        $bestSellingProducts = paginate($request, $bestSellingProducts,9);
        return response(['status'=>'success','data'=>$bestSellingProducts]);
    }

    /**
     * Get incredible offer products
     * @return Response
     */
    public function incredibleOffer(Request $request){
        $incredibleOffer = ProductService::product()->withRelations(['productComments'])->published()->activeSeller()
            ->where(['amazing_offer_status',StatusEnum::ACTIVE],['amazing_offer_expire','>',now()->timestamp])
            ->get()->map(function($item){
                $item['rating'] = $this->calcProductRating($item->productComments)[1];
                return $item->only('id','image','ir_name','count','price','amazing_price','amazing_offer_percent','slug','amazing_offer_status','amazing_offer_expire','rating');
            })->toArray();
        $incredibleOffer = paginate($request, $incredibleOffer,9);
        return response(['status'=>'success','data'=>$incredibleOffer]);
    }

    /**
     * Get special product products
     * @return Response
     */
    public function specialProduct(Request $request){
        $specialProduct = ProductService::product()->withRelations(['productComments'])->published()->activeSeller()
            ->whereNotNull('amazing_price')->where('amazing_offer_status','!=','yes')
            ->get()->map(function($item){
                $item['rating'] = $this->calcProductRating($item->productComments)[1];
                return $item->only('id','image','ir_name','count','price','amazing_price','amazing_offer_percent','slug','amazing_offer_status','amazing_offer_expire','rating');
            })->toArray();
        $specialProduct = paginate($request, $specialProduct,9);
        return response(['status'=>'success','data'=>$specialProduct]);
    }

    /**
     * Get page by id
     * @param Page $page
     * @return Response
     */
    public function getPage(Page $page){
        return response(['status'=>'success','data'=>$page]);
    }

    /**
     * Checking whether the token has expired or not
     * @param $user
     * @return bool
     */
    private function tokenIsExpire($user){
        $token = $user->tokens()->first();
        $tokenIsExpire = $token->created_at->addHour(3) > now();
        if($tokenIsExpire){
            $token->created_at = now();
            $token->save();
            return true;
        }
        return false;
    }

    /**
     *
     * Get the required data `widgets'
     * @param $widgets
     * @return array|mixed
     */
    private function getWidgets($widgets): mixed
    {
        $columns = ['id','price','amazing_price','ir_name','image','slug','amazing_offer_percent','amazing_offer_status','amazing_offer_expire'];
        $widgets = json_decode($widgets['data']??"[]", true);
        foreach ($widgets as $key => $widget) {
            if (in_array($widget['name'], ['amazing_supermarket', 'amazing_offer'])) {
                $widgets[$key]['products'] = ProductService::product()->published()->activeSeller()->findById(...array_column($widget['product_ids'], 'value'))->get(...$columns);
                if ($widget['name'] == 'amazing_supermarket') {
                    $widgets[$key]['product_length'] = ProductService::product()->published()->activeSeller()->byCategory([$widget['category_id']['value']])->withRelations(['category'])->get()->count();
                    $widgets[$key]['category'] = CategoryService::category()->findById($widget['category_id']['value'])->first('id','slug');
                }
            } else if ($widget['name'] == 'shop_category') {
                $widgets[$key]['categories'] = CategoryService::category()->findById(...array_column($widget['category_ids'], 'value'))->get('id', 'image', 'slug', 'title');
            } else if ($widget['name'] == 'brand') {
                $widgets[$key]['brands'] = Brand::where('status',StatusEnum::ACTIVE->value)->whereIn('id', array_column($widget['brand_ids'], 'value'))->get(['id', 'logo', 'en_name']);
            }
        }
        return $widgets;
    }

    /**
     * @param string $slug
     * @return mixed
     */
    private function getCategoryBySlug(string $slug): mixed
    {
        return Category::where('status',StatusEnum::ACTIVE->value)->where('slug', $slug)->first();
    }

    /**
     * @param Product $product
     * @return array
     */
    private function calculateRatingAndSuggestion(Product $product): array
    {
        $productComments = $product->productComments()->where('status',StatusEnum::ACTIVE);
        $commentCount = $productComments->count();
        $suggestionPercent = 0;
        list($average, $rating) = $this->calcProductRating($productComments);
        $suggestions = $productComments->whereNotNull('suggest')->get();
        $totalSuggestions = $suggestions->where('suggest','yes')->count();
        if($totalSuggestions > 0 or $suggestions->count() > 0){
            $suggestionPercent = round(($totalSuggestions/ $suggestions->count())*100,2);
        }
        return [
            $commentCount,
            ['rating'=>round($rating,1), 'average'=>number_format($average)],
            ['percent'=>$suggestionPercent, 'total'=>$totalSuggestions]
        ];
    }

    /**
     * @param $productComments
     * @return array
     */
    private function calcProductRating($productComments): array
    {
        $average = $productComments->sum('rating');
        $rating = 1;
        if ($average > 0 or $productComments->count() > 0) {
            $rating = $average / $productComments->count();
        }
        return [$average, $rating];
    }
}
