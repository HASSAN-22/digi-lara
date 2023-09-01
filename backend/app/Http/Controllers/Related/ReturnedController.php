<?php

namespace App\Http\Controllers\Related;

use App\Enums\DebtorStatusEnum;
use App\Enums\ShippingEnum;
use App\Http\Controllers\Controller;
use App\Models\Debtor;
use App\Models\Order;
use App\Models\Returned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Enum;

class ReturnedController extends Controller
{

    /**
     * Get order details
     * @param Order $order
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function view(Order $order){
        $this->authorize('viewAny',$order);
        $orderDetails = $order->orderDetails()->doesntHave('returned')->get();
        return response(['status'=>'success','data'=>$orderDetails]);
    }

    /**
     * Store returned requests
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request){
        $request->merge(['order_detail_ids'=>json_decode($request->order_detail_ids,true)]);
        $request->validate([
            'problem_description'=>['required','string','max:2000'],
            'order_detail_ids'=>['required','array'],
            'order_detail_ids.*'=>['required','numeric','exists:orderdetails,id'],
        ]);
        $this->authorize('create',Returned::class);
        DB::beginTransaction();
        try{
            $data = [];
            foreach ($request->order_detail_ids as $id){
                $data[] = [
                    'orderdetail_id'=>$id,
                    'order_id'=>$request->order_id,
                    'description'=>$request->problem_description,
                    'status'=>ShippingEnum::PENDING_DELIVERY_TO_STORE,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ];
            }
            Returned::insert($data);
            $order = Order::with('orderDetails')->find($request->order_id);
            $order->orderDetails()->whereIn('id',$request->order_detail_ids)->update(['shipping_status'=>ShippingEnum::RETURNED]);
            $orderDetailCount = $order->orderDetails()->whereNotIn('id',$request->order_detail_ids)->get()->count();
            if($orderDetailCount <= 0){
                $order->update(['shipping_status'=>ShippingEnum::RETURNED]);
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    /**
     * Update status
     * @param Request $request
     * @param Returned $returned
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateStatus(Request $request, Returned $returned){
        $request->validate([
            'status'=>['required','string','max:255', new Enum(ShippingEnum::class)]
        ]);
        $this->authorize('update',$returned);
        DB::beginTransaction();
        try{
            $returned->update(['status'=>ShippingEnum::from($request->status)]);
            if($request->status == ShippingEnum::SUCCESS_PAY_BACK->value){
                $order = $returned->order;
                $orderDetail = $returned->orderDetail;
                Debtor::create([
                    'user_id'=>$orderDetail->user_id,
                    'order_id'=>$order->id,
                    'amount'=>$orderDetail->amount,
                    'description'=>" بابت مرجوع شدن سفارش با شماره سفارش {$order->id} و شماره مرجوعی {$orderDetail->id} ",
                    'status'=>DebtorStatusEnum::PENDING,
                ]);
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    /**
     * Receive returned description
     * @param Returned $returned
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\Response
     */
    public function orderProblem(Returned $returned){
        return response(['status'=>'success','data'=>$returned]);
    }
}
