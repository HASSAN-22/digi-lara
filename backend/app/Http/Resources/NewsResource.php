<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'category'=>$this->category->title,
            'user'=>$this->user->name,
            'title'=>$this->title,
            'image'=>$this->image,
            'publish'=>typeService($this->publish)->publish('fa')->get(),
            'created_at'=>dateToPersian($this->created_at),
        ];
    }
}
