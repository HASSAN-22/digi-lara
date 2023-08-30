<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtorResource extends JsonResource
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
            'order_id'=>$this->order_id,
            'user'=>$this->user->name,
            'shop_name'=>(!is_null($this->becomeSellers) and $this->becomeSellers->count() > 0) ? $this->becomeSellers[0]->shop_name : 'N/A',
            'amount'=>number_format($this->amount),
            'description'=>$this->description,
            'ir_status'=>typeService($this->status)->debtorSatus('fa')->get(),
            'status'=>$this->status,
            'created_at'=>dateToPersian($this->created_at)
        ];
    }
}
