<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'ref_id'=>$this->ref_id,
            'status'=>typeService($this->status)->transactionStatus('fa')->get(),
            'created_at'=>dateToPersian($this->created_at)
        ];
    }
}
