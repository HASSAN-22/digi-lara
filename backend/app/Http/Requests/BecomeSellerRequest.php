<?php

namespace App\Http\Requests;

use App\Enums\BecomeSellerTypeEnum;
use App\Enums\CompanyTypeEnum;
use App\Enums\GenderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BecomeSellerRequest extends FormRequest
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
            'province_id'=>['required','numeric','exists:provinces,id'],
            'city_id'=>['required','numeric','exists:cities,id'],
            'shop_name'=>['required','string','max:255'],
            'seller_type'=>['required','string',new Enum(BecomeSellerTypeEnum::class)],
            'postal_code'=>['required','numeric','digits:10,10'],
            'phone'=>['required','numeric'],
            'mobile'=>['required','numeric','digits:11,11'],
            'shaba'=>['required','numeric','digits:24'],
            'identity_card_front'=>['required','mimes:png','max:2048'],
            'identity_card_back'=>['required','mimes:png','max:2048'],
            'address'=>['required','string','max:400'],
        ];
        if($this->request->get('seller_type') == 'legal'){
            $validation['company_name'] = ['required','string','max:255'];
            $validation['company_type'] = ['required','string',new Enum(CompanyTypeEnum::class),'max:50'];
            $validation['registration_number'] = ['required','numeric'];
            $validation['national_identity_number'] = ['required','numeric','digits:11,11'];
            $validation['economic_number'] = ['required','numeric','digits:12,12'];
            $validation['authorized_representative'] = ['required','string','max:10000'];
        }else{
            $validation['name'] = ['required','string','max:255'];
            $validation['last_name'] = ['required','string','max:255'];
            $validation['birth_day'] = ['required','string','max:255'];
            $validation['gender'] = ['required','string','max:30',new Enum(GenderEnum::class)];
            $validation['identity_card_number'] = ['required','numeric'];
            $validation['national_identity_number'] = ['required','numeric'];
        }

        return $validation;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        if($this->request->get('seller_type') === 'legal'){
            return [
              'national_identity_number.required'=>'فیلد شناسه ملی الزامی است',
              'national_identity_number.numeric'=>'فیلد شناسه ملی باید عدد باشد'
            ];
        }else{
            return [
                'national_identity_number.required'=>'فیلد شماره شناسنامه الزامی است',
                'national_identity_number.numeric'=>'فیلد شماره شناسنامه باید عدد باشد'
            ];
        }
    }
}
