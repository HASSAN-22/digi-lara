<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletRequest extends FormRequest
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
            'amount'=>['required','numeric','max:'.config('app.max_amount'),'min:'.config('app.min_amount')]
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'فیلد مبلغ الزامی است',
            'amount.numeric' => 'فیلد مبلغ باید عدد باشد',
            'amount.max' => "مبلغ نباید بیشتر از ".number_format(config('app.max_amount'))."ریال باشد ",
            'amount.min' => "مبلغ نباید کمتر از ".number_format(config('app.min_amount'))."ریال باشد ",
        ];
    }
}
