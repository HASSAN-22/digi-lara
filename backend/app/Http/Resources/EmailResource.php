<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmailResource extends JsonResource
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
            'issue'=>$this->issue,
            'email'=>$this->email,
            'message'=>$this->message,
            'status'=>$this->status,
            'ir_status'=>typeService($this->status)->emailStatus('fa')->get(),
            'created_at'=>dateToPersian($this->created_at)
        ];
    }
}
