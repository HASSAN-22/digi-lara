<?php

namespace App\Http\Requests;

use App\Enums\WeightTypeEnum;
use App\Enums\YesOrNoEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TransportRequest extends FormRequest
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
        return [
            'transport_name'=>['required','string','max:255'],
            'weight_type'=>['required','string','max:30',new Enum(WeightTypeEnum::class)],
            'is_free'=>['required_if:weight_type,==,style','nullable','string',new Enum(YesOrNoEnum::class)],
            'is_freight'=>['required_if:weight_type,==,heavy || required_if:weight_type,==,super_heavy','nullable','string',new Enum(YesOrNoEnum::class)],
            'fixed_price'=>['required_if:is_freight,==,no || required_if:is_free,==,no','nullable','numeric'],
            'tax'=>['required','numeric','digits_between:0,100'],
            'from_day'=>['required','numeric'],
            'to_day'=>['required','numeric'],
        ];
    }
}
