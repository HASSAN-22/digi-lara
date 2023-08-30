<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'subject'=>$this->subject,
            'username'=>$this->username,
            'email'=>$this->email,
            'mobile'=>$this->mobile,
            'message'=>$this->message,
            'created_at'=>dateToPersian($this->created_at),
            'answer_status'=>is_null($this->answer) ? 'در انتظار پاسخ' : 'پاسخ داده شد',
        ];
    }
}
