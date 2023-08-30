<?php

namespace App\Http\Requests;

use App\Enums\IsSelectedAddressEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class AddressRequest extends FormRequest
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
            'province_id'=>['required','numeric','exists:provinces,id'],
            'city_id'=>['required','numeric','exists:cities,id'],
            'address'=>['required','string','max:2000'],
            'plaque'=>['required','numeric'],
            'unit'=>['nullable','numeric'],
            'postal_code'=>['required','numeric'],
            'receiver_name'=>['required','string','max:255'],
            'receiver_last_name'=>['required','string','max:255'],
            'mobile'=>['required','string','digits:11,11'],
            'is_selected'=>['nullable','string', new Enum(IsSelectedAddressEnum::class)],
        ];
    }
}
