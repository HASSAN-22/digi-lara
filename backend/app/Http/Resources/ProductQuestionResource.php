<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductQuestionResource extends JsonResource
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
            'product'=>$this->product->ir_name,
            'user'=>$this->user->name,
            'status'=>$this->status,
            'ir_status'=>typeService($this->status)->statusConfirm('fa')->get(),
            'question'=>$this->question,
            'created_at'=>dateToPersian($this->created_at),
            'answers_count'=>$this->product_question_answers_count
        ];
    }
}
