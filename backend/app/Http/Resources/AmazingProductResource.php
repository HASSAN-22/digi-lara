<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AmazingProductResource extends JsonResource
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
            'ir_name'=>$this->ir_name,
            'image'=>$this->image,
            'sku'=>$this->sku,
            'amazing_offer_percent'=>is_null($this->amazing_offer_percent) ? '---' : $this->amazing_offer_percent . '%',
            'ir_status'=>typeService($this->amazing_offer_status)->statusConfirm('fa')->get(),
            'status'=>$this->amazing_offer_status,
            'updated_at'=>dateToPersian($this->updated_at)
        ];
    }
}
