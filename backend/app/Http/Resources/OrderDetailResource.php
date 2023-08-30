<?php

namespace App\Http\Resources;

use App\Enums\IsSelectedAddressEnum;
use App\Enums\ShippingEnum;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $transports = Transport::get();
        $diffTime = $this->created_at->addHours(1)->diff();
        $amount = $this->orderDetails->sum('amount');
        $transaction = $this->transactions()->where('user_id',$this->user_id)->first();

        $results = [];
        $results['heavies'] = $this->orderDetails->where('product.category.weight_type','heavy')->values();
        $results['superHeavies'] = $this->orderDetails->where('product.category.weight_type','super_heavy')->values();
        $results['styles'] = $this->orderDetails->where('product.category.weight_type','style')->values();
        $details = [];
        $useReducedWallet = false;
        foreach ($results as $orderDetails){
            if($orderDetails->count() > 0){
                $orderDetail = $orderDetails[0];
                $weightType = $orderDetail->product->category->weight_type;
                $transport = $transports->where('weight_type',$weightType)->first();
                $details[] = [
                    'id'=>$orderDetail->id,
                    'transport_name'=>$transport->name,
                    'weight_type'=>$transport->weight_type,
                    'from_day'=>$transport->from_day,
                    'to_day'=>$transport->to_day,
                    'shipping_status'=>$orderDetail->shipping_status,
                    'amount'=>number_format(!$useReducedWallet ? ($orderDetails->sum('amount') - $this->reduced_wallet) : $orderDetails->sum('amount')),
                    'transport_cost'=>$transport ? $transport->fixed_price : 0,
                    'shipping_status_icon'=>ShippingEnum::getIcon($orderDetail['shipping_status']),
                    'ir_shipping_status'=>typeService($orderDetail['shipping_status'])->shippingStatus('fa')->get(),
                    'data'=>$orderDetails,
                ];
                $useReducedWallet = true;
            }
        }
        return [
            'id'=>$this->id,
            'shipping_status'=>$this->shipping_status,
            'transport_cost'=>$this->transport_cost,
            'reduced_wallet'=>$this->reduced_wallet,
            'ir_created_at'=>dateToPersian($this->created_at),
            'amount'=>number_format($amount - $this->reduced_wallet),
            'address'=>json_decode($this->address,true),
            'left_time'=>$diffTime->h > 0 ? 0 : $diffTime->i,
            'transaction'=>$transaction,
            'ir_transaction_created_at'=>$transaction ? dateToPersian($transaction->created_at) : '',
            'shipping_status_icon'=>ShippingEnum::getIcon($this['shipping_status']),
            'ir_shipping_status'=>typeService($this['shipping_status'])->shippingStatus('fa')->get(),
            'orderDetails'=>$details
        ];
    }
}
