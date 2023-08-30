<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductCommentResource extends JsonResource
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
            'product'=>$this->product->ir_name,
            'comment'=>$this->comment,
            'status'=>$this->status,
            'ir_status'=>typeService($this->status)->statusConfirm('fa')->get(),
            'created_at'=>$this->ir_created_at
        ];
    }
}
