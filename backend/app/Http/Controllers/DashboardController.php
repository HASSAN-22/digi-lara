<?php

namespace App\Http\Controllers;

use App\Enums\DebtorStatusEnum;
use App\Enums\ShippingEnum;
use App\Enums\StatusEnum;
use App\Enums\UserAccessEnum;
use App\Models\Debtor;
use App\Models\Order;
use App\Models\Orderdetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(){
        $user = auth()->user();
        $wallet = $user->wallet;
        $walletAmount = !is_null($wallet) ? number_format($wallet->amount) : 0;
        return response(['status'=>'success','data'=>['wallet_amount'=>$walletAmount,...getShopConfig()]]);
    }

    public function content(){
        $user = auth()->user();
        $orderCount = 0;
        $productCount = 0;
        $userCount = 0;
        $debtor = 0;
        $orders = Order::with(['orderDetails'=>fn($q)=>$q->with(['product'=>fn($q)=>$q->select('id','slug')])])->withCount('orderDetails');
        if(!$user->isUser()){
            $orderCount = $user->isAdmin() ? (clone $orders)->count() : (clone $orders)->whereRelation('orderDetails','user_id',$user->id)->get()->count();
            $productCount = $user->isAdmin() ? Product::count() : $user->products()->count();
            $debtor = $user->isAdmin() ? number_format(Debtor::where('status',DebtorStatusEnum::PENDING)->sum('amount')) : number_format($user->debtors()->where('status',DebtorStatusEnum::PENDING)->sum('amount'));
        }
        if($user->isAdmin()){
            $userCount = User::count();
            $latestOrders = (clone $orders)->get();
            $current = (clone $orders)->where('shipping_status',ShippingEnum::PAYMENT_PENDING)->count();
            $delivered = (clone $orders)->where('shipping_status',ShippingEnum::DELIVERED_CUSTOMER)->count();
            $returned = Orderdetail::whereIn('order_id',(clone $orders)->get()->pluck('id'))->where('shipping_status',ShippingEnum::RETURNED)->count();

        }else{
            $userOrder = (clone $orders)->where('user_id',$user->id);
            $latestOrders = (clone $userOrder)->get();
            $current = (clone $userOrder)->where('shipping_status',ShippingEnum::PAYMENT_PENDING)->count();
            $delivered = (clone $userOrder)->where('shipping_status',ShippingEnum::DELIVERED_CUSTOMER)->count();
            $returned = Orderdetail::whereIn('order_id',(clone $userOrder)->get()->pluck('id'))->where('shipping_status',ShippingEnum::RETURNED)->count();
        }
        $latestOrders = $latestOrders->map(fn($i)=>$i->orderDetails)->toArray();
        $latestOrders = call_user_func_array('array_merge', $latestOrders);
        $latestOrders = array_slice($latestOrders,-4);
        return response([
            'order_count'=>$orderCount,
            'product_count'=>$productCount,
            'user_count'=>$userCount,
            'debtor'=>$debtor,
            'current'=>$current,
            'delivered'=>$delivered,
            'returned'=>$returned,
            'latest_orders'=>$latestOrders
        ]);
    }

}
