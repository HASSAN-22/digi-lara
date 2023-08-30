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
    public function store(Request $request){
        $request->validate(['problem_description'=>['required','string','max:2000']]);
        $this->authorize('create',Returned::class);
        DB::beginTransaction();
        try{
            $returneds = Returned::create([
                'order_id'=>$request->order_id,
                'description'=>$request->problem_description,
                'status'=>ShippingEnum::PENDING_DELIVERY_TO_STORE,
            ]);
            $order = Order::with('orderDetails')->find($request->order_id);
            $order->update(['shipping_status'=>ShippingEnum::RETURNED]);
            $order->orderDetails()->update(['shipping_status'=>ShippingEnum::RETURNED]);
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    public function updateStatus(Request $request, Returned $returned){
        $request->validate([
            'status'=>['required','string','max:255', new Enum(ShippingEnum::class)]
        ]);
        $this->authorize('update',$returned);
        DB::beginTransaction();
        try{
            $returned->update(['status'=>ShippingEnum::from($request->status)]);
            if($request->status == ShippingEnum::SUCCESS_PAY_BACK->value){
                Log::info($request->status);
                $order = $returned->order;
                $wallet = $order->wallet;
                if($wallet){
                    $wallet->update(['amount'=>DB::raw("amount + {$order->reduced_wallet}")]);
                    $order->reduced_wallet = 0;
                    $order->save();
                }
                $orderDetails = $order->orderDetails;
                $debtors = $orderDetails->values()->pluck('user_id')->unique()->map(function ($id)use($orderDetails){
                    $results = (clone $orderDetails)->where('user_id',$id)->values();
                    $orderId = $results[0]->order_id;
                    return [
                        'user_id'=>$id,
                        'order_id'=>$orderId,
                        'amount'=>$results->sum('amount'),
                        'description'=>" بابت مرجوع شدن سفارش {$orderId}",
                        'status'=>DebtorStatusEnum::PENDING,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ];
                });
                Debtor::insert($debtors->toArray());
            }
            DB::commit();
            return response(['status'=>'success'],201);
        }catch (\Exception $e){
            DB::rollBack();
            return response(['status'=>'error'],500);
        }
    }

    public function orderProblem(Returned $returned){
        return response(['status'=>'success','data'=>$returned]);
    }
}
