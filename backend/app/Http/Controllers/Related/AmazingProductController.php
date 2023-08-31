<?php

namespace App\Http\Controllers\Related;

use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AmazingProductController extends Controller
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Request $request){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'viewAnyAmazingProduct' : 'viewAnySellerAmazingProduct',Product::class);
        $categories = Category::where('status',StatusEnum::ACTIVE->value)->get();
        $search = trim(request()->get('search'));
        $query = DB::table('products');
        if($search != ''){
            $query->where('sku','like',"%$search%")->orWhere('ir_name','like',"%$search%");
        }
        $query = $user->isSeller() ? $query->where('user_id', $user->id) : $query;

        $products =$query->select(['id', 'ir_name','image','sku','amazing_offer_percent'])
            ->where('amazing_offer_status',StatusEnum::ACTIVE->value)
            ->paginate(10);
        return response(['status'=>'success','data'=>['categories'=>$categories,'products'=>$products]]);
    }

    public function store(Request $request){
        $this->authorize('createAmazingProduct',Product::class);
        $request['product_ids'] = json_decode($request->product_ids,true);
        $request->validate([
           'product_ids'=>['required','array'],
           'product_ids.*'=>['required','numeric','exists:products,id'],
           'category_id'=>['required','numeric','exists:categories,id']
        ]);
        try{
            Product::whereIn('id',$request->product_ids)
            ->update(['amazing_offer_status'=>StatusEnum::ACTIVE,'amazing_offer_expire'=>now()->addHours(24)->timestamp]);
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            return response(['status'=>'error','e'=>$e->getMessage()],500);
        }
    }

    /**
     * Undo from amazing mode
     * @param Product $product
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Product $product){
        $user = auth()->user();
        $this->authorize($user->isAdmin() ? 'deleteAmazingProduct' : 'deleteSellerAmazingProduct',$product);
        $products = $product->update(['amazing_offer_status'=>null,'amazing_offer_percent'=>null]);
        return $products ? response(['status'=>'success']) : response(['status'=>'error'],500);
    }

    /**
     * Get products by category
     * @param Category $category
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getProduct(Category $category){
        $this->authorize('viewAnyAmazingProduct',Product::class);
        $products = $category->products()->where('amazing_offer_status',StatusEnum::PENDING->value)->get();
        return response(['status'=>'success','data'=>$products]);
    }
}
