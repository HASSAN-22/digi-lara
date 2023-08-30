<?php

namespace App\Http\Requests;

use App\Enums\PublishEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class NewsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $validation = [
            'category_id'=>['required','numeric','exists:categories,id'],
            'title'=>['required','string','max:255','unique:news,id'],
            'short_description'=>['required','string','max:300'],
            'description'=>['required','string','max:30000'],
            'image'=>['required','mimes:jpg,jpeg,png','max:'.config('app.image_size'),'dimensions:max_width=826,max_height=514'],
            'publish'=>['required','string','max:50',new Enum(PublishEnum::class)],
        ];

        if($this->isMethod('PATCH')){
            $validation['image'][0] = 'nullable';
            $validation['title'][4] = 'unique:news,title,id';
        }

        return $validation;
    }
}
