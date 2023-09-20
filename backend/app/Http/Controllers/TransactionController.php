<?php

namespace App\Http\Controllers;

use App\Enums\DebtorStatusEnum;
use App\Enums\ShippingEnum;
use App\Models\Debtor;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Services\Bank\Payment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionController extends Controller
{

    /**
     * After payment verified
     * @param Request $request
     * @return RedirectResponse
     * @throws \Exception
     */
    public function verify(Request $request){
        $result = $request->all();
        $result['merchantId'] = config('services.gateway.merchant_id');
        try {
            $response = Payment::driver('Zibal')->verify($result);
            $responseData = $response['data'];
            $responseData['refNumber'] = $responseData['refNumber'] ?? Str::random(8);
            if($result['action'] == 'order'){
                $this->updateOrder($responseData);
            }elseif($result['action'] == 'debtor'){
                $this->updateDebtor($responseData);
            }elseif($result['action'] == 'wallet'){
                $this->updateWallet($responseData);
            }
            $path = config('app.front_url').'payment-alert?status='.$response['status'].'&msg='."با موفقیت پرداخت شد. کد پیگیری: {$responseData['refNumber']}";
            return redirect()->to($path)->send();
        }catch (\Exception $e){
            $path = config('app.front_url').'payment-alert?status=error'.'&msg='."{$e->getMessage()}";
            return redirect()->to($path)->send();
        }
    }

    private function updateOrder(array $response){
        $order = Order::with(['orderDetails','user'])->find($response['orderId']);
        $user = $order->user;
        $order->update(['shipping_status' => ShippingEnum::REVIEW_QUEUE]);
        $order->orderDetails()->update(['shipping_status' => ShippingEnum::REVIEW_QUEUE]);
        insertTransaction($order, (int) $user->id, (int) $response['amount'], $response['refNumber']);
        $productIds = $order->orderDetails->map(fn($item)=>$item->product_id)->toArray();
        Product::whereIn('id',$productIds)->update(['count'=>DB::raw("count - 1")]);
    }

    private function updateWallet(array $response){
        $wallet = Wallet::find($response['orderId']);
        $wallet->amount += $response['amount'];
        $wallet->save();
        insertTransaction($wallet, (int) $wallet->user_id, (int) $response['amount'], $response['refNumber']);
    }

    private function updateDebtor(array $response)
    {
        $debtor = Debtor::find($response['orderId']);
        $debtor->update(['amount'=>0,'status'=>DebtorStatusEnum::SETTLED]);
        insertTransaction($debtor, (int) $debtor->user_id, (int)$response['amount'], $response['refNumber']);

    }

}
