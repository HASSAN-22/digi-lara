<?php

namespace App\Http\Controllers\Related;

use App\Enums\IsSelectedAddressEnum;
use App\Enums\PublishEnum;
use App\Enums\ShippingEnum;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderDetailReturnedResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Product;
use App\Models\Returned;
use App\Models\Shopconfig;
use App\Models\User;
use App\Services\Bank\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{

    /**
     * Get order for invoice
     * @param Order $order
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function invoice(Order $order){
        $this->authorize('view',$order);
        $user = auth()->user();
        $userId = $user->isAdmin() ? $order->user_id : $user->id;
        $order = $order->where('id',$order->id)->with(['orderDetails'=>fn($q)=>$q->with(['user'=>fn($q)=>$q->whereRelation('becomeSellers','status',StatusEnum::ACTIVE)->with('becomeSellers')])])->first();
        $shopInfo = json_decode(Shopconfig::getValue('legal_info')->value,true);
        $shopInfo['ir_company_type'] = typeService($shopInfo['company_type'])->companyType('fa')->get();
        $buyer = User::with(['profile','legalInfo'])->find($userId);
        $order['ir_created_at'] = dateToPersian($order->created_at);
        $buyerInfo = [
            'name'=>$buyer->name,
            'economic_national_id'=>$buyer->legalInfo->economic_code ?? $buyer->profile->national_id,
            'national_id'=>$buyer->legalInfo->national_id ?? $buyer->profile->national_id,
            'registration_id'=>$buyer->legalInfo->registration_id ?? '---',
            'address'=>json_decode($order->address),
        ];
        $shopSetting = json_decode(Shopconfig::getValue('store_detail')->value,true);
        return response(['status'=>'success','data'=>['order'=>$order,'shop_info'=>$shopInfo,'buyer'=>$buyerInfo,'logo'=>$shopSetting['logo']]]);
    }

    /**
     * Get order and order details
     * @param Order $order
     * @return OrderDetailResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getDetail(Order $order){
        $this->authorize('view',$order);
        $order = $order->where('id',$order->id)->with([
            'user'=>fn($q)=>$q->select('id'),
            'orderDetails'=>fn($q)=>$q->with(['product'=>fn($q)=>$q->select('id','ir_name','slug','brand_id','category_id','guarantee_id')
                ->with(['brand','guarantee','category'=>fn($q)=>$q->select('id','weight_type')])])
        ])->first();
        return new OrderDetailResource($order);
    }

    /**
     * Receive current orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getCurrent(){
        $this->authorize('viewAny',Order::class);
        $exceptStatus = [
            ShippingEnum::DELIVERED_CUSTOMER,
            ShippingEnum::CANCELLED,
            ShippingEnum::AUTO_CANCELLED,
            ShippingEnum::RETURNED,
            ShippingEnum::PENDING_DELIVERY_TO_STORE,
            ShippingEnum::PENDING_PAY_BACK,
            ShippingEnum::SUCCESS_PAY_BACK
        ];

        $currentOrders = auth()->user()->orders()->whereNotIn('shipping_status',$exceptStatus)->with(['orderDetails'=>fn($q)=>$q->select('id','order_id','image','amount'),'returned'])->paginate(10);
        return OrderResource::collection($currentOrders);
    }


    /**
     * Receive delivered orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getDelivered(){
        $this->authorize('viewAny',Order::class);
        $currentOrders = auth()->user()->orders()->where('shipping_status',ShippingEnum::DELIVERED_CUSTOMER)->with(['orderDetails'=>fn($q)=>$q->select('id','order_id','image','amount'),'returned'])->paginate(10);
        return OrderResource::collection($currentOrders);
    }

    /**
     * Receive returned orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getReturned(){
        $this->authorize('viewAny',Order::class);
        $status = [ShippingEnum::RETURNED,ShippingEnum::PENDING_DELIVERY_TO_STORE,ShippingEnum::PENDING_PAY_BACK,ShippingEnum::SUCCESS_PAY_BACK];
        $returndes = Returned::whereRelation('order','user_id',auth()->Id())
            ->with(['orderDetail'=>fn($q)=>$q->select('id','image','price','shipping_status'),'order'=>fn($q)=>$q->select('id','reduced_wallet')])->whereIn('status',$status)->latest()->paginate(10);
        return OrderDetailReturnedResource::collection($returndes);
    }

    /**
     * Receive canceled orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getCanceled(){
        $this->authorize('viewAny',Order::class);
        $currentOrders = auth()->user()->orders()->whereIn('shipping_status',[ShippingEnum::AUTO_CANCELLED,ShippingEnum::CANCELLED])->with(['orderDetails'=>fn($q)=>$q->select('id','order_id','image','amount'),'returned'])->paginate(10);
        return OrderResource::collection($currentOrders);
    }


    /**
     * Receive current orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getCustomerCurrent(){
        $this->authorize('viewAnyAdminOrSeller',Order::class);
        $exceptStatus = [
            ShippingEnum::DELIVERED_CUSTOMER,
            ShippingEnum::CANCELLED,
            ShippingEnum::AUTO_CANCELLED,
            ShippingEnum::RETURNED,
            ShippingEnum::PENDING_DELIVERY_TO_STORE,
            ShippingEnum::PENDING_PAY_BACK,
            ShippingEnum::SUCCESS_PAY_BACK
        ];
        $currentOrders = $this->orderQuery()->whereNotIn('shipping_status',$exceptStatus)->with(['orderDetails'=>fn($q)=>$q->select('id','order_id','image','amount'),'returned'])->paginate(10);
        return OrderResource::collection($currentOrders);
    }


    /**
     * Receive delivered orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getCustomerDelivered(){
        $this->authorize('viewAnyAdminOrSeller',Order::class);
        $currentOrders = $this->orderQuery()->where('shipping_status',ShippingEnum::DELIVERED_CUSTOMER)->with(['orderDetails'=>fn($q)=>$q->select('id','order_id','image','amount'),'returned'])->paginate(10);
        return OrderResource::collection($currentOrders);
    }

    /**
     * Receive returned orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getCustomerReturned(){
        $this->authorize('viewAnyAdminOrSeller',Order::class);
        $status = [ShippingEnum::RETURNED,ShippingEnum::PENDING_DELIVERY_TO_STORE,ShippingEnum::PENDING_PAY_BACK,ShippingEnum::SUCCESS_PAY_BACK];
        $returndes = Returned::with(['orderDetail'=>fn($q)=>$q->select('id','image','price','shipping_status'),'order'=>fn($q)=>$q->select('id','reduced_wallet')])
            ->whereIn('status',$status)->latest()->paginate(10);
        return OrderDetailReturnedResource::collection($returndes);
    }

    /**
     * Receive canceled orders
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function getCustomerCanceled(){
        $this->authorize('viewAnyAdminOrSeller',Order::class);
        $currentOrders = $this->orderQuery()->whereIn('shipping_status',[ShippingEnum::AUTO_CANCELLED,ShippingEnum::CANCELLED])->with(['orderDetails'=>fn($q)=>$q->select('id','order_id','image','amount'),'returned'])->paginate(10);
        return OrderResource::collection($currentOrders);
    }


    /**
     * Cancel order
     * @param Order $order
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancelOrder(Order $order){
        $this->authorize('updateAdminOrSeller',$order);
        try {
            $order->shipping_status = ShippingEnum::CANCELLED;
            $wallet = $order->user->wallet();
            if($wallet){
                $wallet->update(['amount'=>DB::raw("amount + {$order->reduced_wallet}")]);
                $order->reduced_wallet = 0;
            }
            $order->save();
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            response(['status'=>'error'],500);
        }
    }

    /**
     * Show order
     * @param Order $order
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showOrder(Order $order){
        $this->authorize('viewAdminOrSeller',$order);
        return response(['status'=>'success','data'=>$order]);
    }

    /**
     * Update status of order
     * @param Request $request
     * @param Order $order
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateOrder(Request $request,Order $order){
        $this->authorize('updateAdminOrSeller',$order);
        DB::beginTransaction();
        try{
            $order->shipping_status = ShippingEnum::from($request->shipping_status);
            if(in_array($request->status,[ShippingEnum::CANCELLED->value, ShippingEnum::AUTO_CANCELLED->value])){
                $wallet = $order->user->wallet();
                if($wallet){
                    $wallet->update(['amount'=>DB::raw("amount + {$order->reduced_wallet}")]);
                    $order->reduced_wallet = 0;
                }
            }
            $order->save();
            $order->orderDetails()->update(['shipping_status'=>$request->shipping_status]);
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    ///////////////////////////////////////////////// Order Detail /////////////////////////////////////////////////////

    /**
     * Cancel order detail
     * @param Orderdetail $orderdetail
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancelDetailOrder(Request $request,Orderdetail $orderdetail){
        $order = $orderdetail->order;
        $this->authorize('updateAdminOrSeller',$order);
        DB::beginTransaction();
        try{
            $orderdetails = $order->orderdetails;
            $ids = (clone $orderdetails)->where('product.category.weight_type',$request->weight_type)->values()->pluck('id')->filter(fn($item)=>$item)->toArray();
            if(count($ids) > 0){
                Orderdetail::whereIn('id',$ids)->update(['shipping_status'=>ShippingEnum::CANCELLED]);
                if(!$this->walletPayBack($orderdetails, $ids, $order)){
                    DB::rollBack();
                    return response(['status'=>'error'],500);
                }
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    /**
     * Show order detail
     * @param Orderdetail $orderdetail
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function showDetailOrder(Orderdetail $orderdetail){
        $this->authorize('viewAdminOrSeller',$orderdetail->order);
        return response(['status'=>'success','data'=>$orderdetail]);
    }

    /**
     * Update status of order detail
     * @param Request $request
     * @param Orderdetail $orderdetail
     * @return Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateDetailOrder(Request $request,Orderdetail $orderdetail){
        $order = $orderdetail->order;
        $this->authorize('updateAdminOrSeller',$order);
        DB::beginTransaction();
        try{
            $orderdetails = $order->orderdetails;
            $ids = (clone $orderdetails)->where('product.category.weight_type',$request->weight_type)->values()->pluck('id')->filter(fn($item)=>$item)->toArray();
            if(count($ids) > 0){
                Orderdetail::whereIn('id',$ids)->update(['shipping_status'=>ShippingEnum::from($request->shipping_status)]);
                if(in_array($request->status,[ShippingEnum::CANCELLED->value, ShippingEnum::AUTO_CANCELLED->value])){
                    if(!$this->walletPayBack($orderdetails, $ids, $order)){
                        DB::rollBack();
                        return response(['status'=>'error'],500);
                    }
                }
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    /**
     * @param Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|Response
     * @throws \Exception
     */
    public function payOrder(Order $order){
        $order->orderDetails()->whereRelation('product','count','<=',0)->delete();
        $order = $order->where(['id'=>$order->id])->with('orderDetails')->first();
        $amount = $order->orderDetails->sum('amount');
        $redirectUrl = Payment::driver('Zibal')->request(setGateway((int) $amount, (int) $order->id, $order->user->mobile, 'order'));
        return response(['status'=>'success','data'=>['redirect_url'=>$redirectUrl]]);
    }

    /**
     * @return Order
     */
    private function orderQuery(): Order
    {
        $user = auth()->user();
        $query = new Order();
        if ($user->isSeller()) {
            $query = $query->whereRelation('orderDetails', 'user_id', $user->id);
        }
        return $query;
    }

    /**
     * Calculate the amount returned to the buyer's wallet for canceled orders
     * @param $orderdetails
     * @param $ids
     * @param mixed $order
     * @return bool
     */
    private function walletPayBack($orderdetails, $ids, mixed $order): bool
    {
        DB::beginTransaction();
        try{
            $amount = (clone $orderdetails)->whereNotIn('id', $ids)->values()->sum('amount');
            $wallet = $order->user->wallet;
            if ($amount < $order->reduced_wallet and $wallet) {
                $amount = $order->reduced_wallet - $amount;
                $wallet->update(['amount' => DB::raw("amount + {$amount}")]);
                $order->reduced_wallet = $amount;
            }
            $order->save();
            DB::commit();
            return true;
        }catch (\Exception $e){
            DB::rollBack();
            return false;
        }
    }
}
