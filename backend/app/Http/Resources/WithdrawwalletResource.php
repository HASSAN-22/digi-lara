<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WithdrawwalletResource extends JsonResource
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
            'user'=>$this->user->name,
            'amount'=>number_format($this->amount),
            'shaba'=>$this->shaba,
            'cart_number'=>$this->cart_number,
            'status'=>$this->status,
            'ir_status'=>typeService($this->status)->withdrawStatus('fa')->get(),
            'created_at'=>dateToPersian($this->created_at)
        ];
    }
}
