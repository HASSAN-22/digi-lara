<?php

namespace App\Http\Requests;

use App\Enums\IsOriginalEnum;
use App\Enums\PublishEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ProductRequest extends FormRequest
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
            'brand_id'=>['required','numeric','exists:brands,id'],
            'guarantee_id'=>['required','numeric','exists:guarantees,id'],
            'ir_name'=>['required','string','max:255','unique:products,ir_name'],
            'en_name'=>['nullable','string','max:255','unique:products,en_name'],
            'sku'=>['required','string','max:255','unique:products,sku'],
            'price'=>['required','numeric'],
            'guarantee_month'=>['required','numeric'],
            'amazing_offer_percent'=>['required_if:is_amazing_offer,==,yes','nullable','numeric','between:0,100'],
            'amazing_price'=>['nullable','numeric'],
            'packing_length'=>['required','numeric'],
            'packing_width'=>['required','numeric'],
            'packing_height'=>['required','numeric'],
            'packing_weight'=>['required','numeric'],
            'physical_length'=>['required','numeric'],
            'physical_width'=>['required','numeric'],
            'physical_height'=>['required','numeric'],
            'physical_weight'=>['required','numeric'],
            'count'=>['required','numeric'],
            'is_original'=>['required','string','max:6', new Enum(IsOriginalEnum::class)],
            'strengths'=>['nullable','array'],
            'strengths.*'=>['required','string','max:255'],
            'weak_points'=>['nullable','array'],
            'weak_points.*'=>['required','string','max:255'],
            'description'=>['required','string','min:200','max:2000'],
            'more_description'=>['nullable','string', 'max:10000'],
            'image'=>['required','mimes:jpg,jpeg,png','max:5120'],
            'images'=>['nullable','array'],
            'images.*.*'=>['required','mimes:jpg,jpeg,png','max:5120'],
            'specification_names'=>['nullable','array'],
            'specification_names.*'=>['required','string','max:255'],
            'specification_description'=>['nullable','array'],
            'specification_description.*'=>['required','string','max:255'],
            'color_ids'=>['nullable','array'],
            'color_ids.*'=>['required','numeric','exists:colors,id'],
            'colors_price'=>['nullable','array'],
            'colors_price.*'=>['required','numeric'],
            'size_ids'=>['nullable','array'],
            'size_ids.*'=>['required','numeric','exists:colors,id'],
            'sizes_price'=>['nullable','array'],
            'sizes_price.*'=>['required','numeric'],
            'property_ids'=>['nullable','array'],
            'property_ids.*'=>['required','numeric','exists:properties,id'],
            'property_types'=>['nullable','array'],
            'property_types.*.*'=>['required','numeric','exists:propertytypes,id'],
        ];

        if(auth()->user()->isAdmin()){
            $validation['seller_id'] = ['required','numeric','exists:users,id'];
            $validation['publish'] = ['required','string','max:255',new Enum(PublishEnum::class)];
        }
        if($this->isMethod('patch')){
            $validation['ir_name'][3] = 'unique:products,id,ir_name';
            $validation['en_name'][3] = 'unique:products,id,en_name';
            $validation['sku'][3] = 'unique:products,id,sku';
            $validation['image'][0] = 'nullable';
        }
        return $validation;
    }


    protected function prepareForValidation()
    {
        $strengths = $this->request->get('strengths');
        $weak_points = $this->request->get('weak_points');
        $specification_names = $this->request->get('specification_names');
        $specification_description = $this->request->get('specification_description');
        $color_ids = $this->request->get('color_ids');
        $colors_price = $this->request->get('colors_price');
        $size_ids = $this->request->get('size_ids');
        $sizes_price = $this->request->get('sizes_price');
        $property_ids = $this->request->get('property_ids');
        $property_types = $this->request->get('property_types');
        $amazing_offer_update = $this->request->get('amazing_offer_update');
        $this->merge(['strengths' => json_decode($strengths, true)]);
        $this->merge(['weak_points' => json_decode($weak_points, true)]);
        $this->merge(['specification_names' => json_decode($specification_names, true)]);
        $this->merge(['specification_description' => json_decode($specification_description, true)]);
        $this->merge(['property_ids' => json_decode($property_ids, true)]);
        $this->merge(['property_types' => json_decode($property_types, true)]);
        $this->merge(['amazing_offer_update' => json_decode($amazing_offer_update)]);
        $this->merge(['color_ids' => json_decode($color_ids)]);
        $this->merge(['colors_price' => json_decode($colors_price)]);
        $this->merge(['size_ids' => json_decode($size_ids)]);
        $this->merge(['sizes_price' => json_decode($sizes_price)]);
    }
}
