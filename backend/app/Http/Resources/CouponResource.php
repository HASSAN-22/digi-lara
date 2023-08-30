<?php

namespace App\Http\Resources;

use App\Enums\CouponTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $percent = ($this->type == CouponTypeEnum::PERCENT->value) ?
            "{$this->percent} %" :
            number_format($this->percent) . " ریال ";

        return [
            'id'=>$this->id,
            'title'=>$this->title,
            'code'=>$this->code,
            'type'=>typeService($this->type)->couponType('fa')->get(),
            'percent'=>$percent,
            'count'=>$this->count,
            'start_at'=>dateToPersian($this->start_at),
            'expire_at'=>dateToPersian($this->expire_at),
        ];
    }
}
