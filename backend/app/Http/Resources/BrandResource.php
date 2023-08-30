<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'ir_name'=>$this->name,
            'en_name'=>$this->en_name,
            'description'=>$this->description,
            'brand_type'=>typeService($this->brand_type)->brandType('fa')->get(),
            'registration_form'=>$this->registration_form,
            'link'=>$this->link,
            'logo'=>$this->logo,
            'reason_rejection'=>$this->reason_rejection ?: '---',
            'status'=>typeService($this->status)->statusConfirm('fa')->get(),
            'created_at'=>dateToPersian($this->created_at)
        ];
    }
}
