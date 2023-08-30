<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BecomesellerResource extends JsonResource
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
            'shop_name'=>$this->shop_name,
            'type'=>typeService($this->type)->sellerType('fa')->get(),
            'mobile'=>$this->mobile,
            'shaba'=>$this->shaba,
            'status'=>$this->status,
            'ir_status'=>typeService($this->status)->status('fa')->get(),
            'reject_reason'=>!is_null($this->reject_reason) ? Str::limit($this->reject_reason, 70,'...') : '---',
            'ir_created_at'=>dateToPersian($this->created_at)
        ];
    }
}
