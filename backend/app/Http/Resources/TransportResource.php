<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransportResource extends JsonResource
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
            'name'=>$this->name,
            'fixed_price'=>number_format($this->fixed_price),
            'is_free'=>typeService($this->is_free)->isFree('fa')->get(),
            'is_freight'=>typeService($this->is_freight)->isFreight('fa')->get(),
            'tax'=>number_format($this->tax),
            'weight_type'=>typeService($this->weight_type)->transportWeightType('fa')->get(),
        ];
    }
}
