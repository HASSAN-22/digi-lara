<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessagesResource extends JsonResource
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
            'is_admin'=>$this->user->access == 'admin',
            'message'=>$this->message,
            'created_at'=>dateToPersian($this->created_at),
            'avatar'=>$this->user->avatar,
            'username'=>$this->user->name,
        ];
    }
}
