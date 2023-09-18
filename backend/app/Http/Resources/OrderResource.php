<?php

namespace App\Http\Resources;

use App\Enums\ShippingEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $largeImageSize = config('app.large_image_size');
        $smallImageSize = config('app.small_image_size');
        $diffTime = $this->created_at->addHours(1)->diff();
        $amount = $this->orderDetails->sum('amount');
        Log::info($amount);
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id,
            'returned_id'=>$this->returned->id ?? 0,
            'ir_returned_status'=>$this->returned ? typeService($this->returned->status)->shippingStatus('fa')->get() : '',
            'returned_status'=>$this->returned->status ?? '',
            'shipping_status'=>$this->shipping_status,
            'ir_shipping_status'=>typeService($this->shipping_status)->shippingStatus('fa')->get(),
            'icon'=>ShippingEnum::getIcon($this->shipping_status),
            'created_at'=>dateToPersian($this->created_at),
            'time_left'=>$diffTime->h > 0 ? 0 : $diffTime->i,
            'can_returned'=>$this->created_at->addDays(config('app.returned_day')) > now(),
            'amount'=>number_format($this->orderDetails->sum('amount')),
            'order_details'=>$this->orderDetails->map(fn($item)=>str_replace("{$largeImageSize}x{$largeImageSize}_","{$smallImageSize}x{$smallImageSize}_",$item->image))
        ];
    }
}
