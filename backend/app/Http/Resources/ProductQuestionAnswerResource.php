<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductQuestionAnswerResource extends JsonResource
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
            'status'=>$this->status,
            'ir_status'=>typeService($this->status)->statusConfirm('fa')->get(),
            'answer'=>$this->answer,
            'created_at'=>dateToPersian($this->created_at)
        ];
    }
}
