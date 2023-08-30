<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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
            'image'=>['required','mimes:jpg,jpeg,png,webp','max:'.config('app.image_size')],
            'link'=>['required','string','max:255']
        ];

        if($this->isMethod('patch')){
            $validation['image'][0] = 'nullable';
        }

        return $validation;
    }
}
