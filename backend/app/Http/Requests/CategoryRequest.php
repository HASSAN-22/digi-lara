<?php

namespace App\Http\Requests;

use App\Enums\WeightTypeEnum;
use App\Enums\StatusEnum;
use App\Enums\CategoryTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CategoryRequest extends FormRequest
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
            'parent_id'=>['required','numeric'],
            'title'=>['required','string','max:255','unique:categories,id'],
            'category_type'=>['required','string','max:255',new Enum(CategoryTypeEnum::class)],
            'image'=>['nullable','mimes:jpg,jpeg,svg,png,webp',"max:".config('app.image_size')],
            'status'=>['required','string','max:255',new Enum(StatusEnum::class)],
            'icon'=>['nullable','string','max:255'],
            'properties'=>['nullable','array'],
            'properties.*'=>['required','numeric','exists:properties,id'],
            'commission'=>['nullable','numeric','between:0,100']
        ];
        if($this->request->get('category_type') == 'category'){
            $validation['weight_type'] = ['required','nullable','string','max:20',new Enum(WeightTypeEnum::class)];
        }
        if($this->isMethod('PATCH')){
            $validation['title'][3] = 'unique:categories,id,title';
        }

        return $validation;
    }

    protected function prepareForValidation()
    {
        $properties = $this->request->get('properties');
        $this->merge(['properties' => json_decode($properties, true)]);
    }
}
