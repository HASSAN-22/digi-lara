<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name'=>$this->name,
            'mobile'=>$this->mobile,
            'access'=>typeService($this->access)->access('fa')->get(),
            'status'=>typeService($this->status)->status('fa')->get(),
            'created_at'=>dateToPersian($this->created_at),
            'avatar'=>$this->avatar,
            'role'=>count($this->roles) > 0 ? $this->roles[0]['name'] : '---',
        ];
    }
}
