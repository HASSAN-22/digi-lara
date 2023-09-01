<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailReturnedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'order_detail_id'=>$this->orderdetail_id,
            'order_id'=>$this->order_id,
            'description'=>$this->description,
            'image'=>$this->orderdetail->image,
            'amount'=>number_format($this->orderdetail->amount),
            'created_at'=>dateToPersian($this->created_at),
            'ir_status'=>typeService($this->status)->shippingStatus('fa')->get(),
            'status'=>$this->status,
            'shipping_status'=>$this->orderdetail->shipping_status,
            'ir_shipping_status'=>typeService($this->orderdetail->shipping_status)->shippingStatus('fa')->get(),
        ];
    }
}
