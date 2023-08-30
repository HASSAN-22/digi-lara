<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'ticket_category'=>$this->ticketCategory->title,
            'user'=>$this->user->name,
            'title'=>$this->title,
            'priority'=>typeService($this->priority)->priority('fa')->get(),
            'ir_is_locked'=>typeService($this->is_locked)->isLocked('fa')->get(),
            'is_locked'=>$this->is_locked,
            'created_at'=>dateToPersian($this->created_at)
        ];
    }
}
