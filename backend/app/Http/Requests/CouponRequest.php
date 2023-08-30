<?php

namespace App\Http\Requests;

use App\Enums\CouponTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CouponRequest extends FormRequest
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
            'title'=>['required','string','max:255'],
            'code'=>['required','string','max:255','unique:coupons,id'],
            'type'=>['required','string','max:255',new Enum(CouponTypeEnum::class)],
            'percent'=>['required','numeric'],
            'count'=>['required','numeric'],
            'start_at'=>['required','string','max:255'],
            'expire_at'=>['required','string','max:255'],
            'products'=>['nullable','array'],
            'products.*'=>['required','numeric','exists:products,id'],
            'categories'=>['nullable','array'],
            'categories.*'=>['required','numeric','exists:categories,id'],
        ];
        if($this->isMethod('PATCH')){
            $validation['code'][3] = 'unique:coupons,id,code';
        }
        return $validation;
    }

    protected function prepareForValidation()
    {
        $products = $this->request->get('products');
        $categories = $this->request->get('categories');
        $this->merge(['products' => json_decode($products, true)]);
        $this->merge(['categories' => json_decode($categories, true)]);
    }
}
