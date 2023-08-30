<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name'=>$this->ir_name,
            'price'=>number_format($this->price),
            'amazing_price'=>(!is_null($this->amazing_price) and $this->amazing_price > 0) ? number_format($this->amazing_price) : null,
            'count'=>number_format($this->count),
            'image'=>$this->image,
            'amazing_offer_percent'=>(!is_null($this->amazing_offer_percent) and $this->amazing_offer_percent > 0) ? number_format($this->amazing_offer_percent) : null,
            'amazing_offer_status'=>typeService($this->amazing_offer_status)->amazingOfferSatus('fa')->get() ?? '',
            'publish'=>typeService($this->publish)->publish('fa')->get(),
            'sku'=>$this->sku,
        ];
    }
}
