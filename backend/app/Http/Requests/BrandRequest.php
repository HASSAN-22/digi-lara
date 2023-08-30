<?php

namespace App\Http\Requests;

use App\Enums\BrandTypeEnum;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class BrandRequest extends FormRequest
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
            'ir_name'=>['required','string','unique:brands,name'],
            'en_name'=>['required','string','unique:brands,en_name'],
            'description'=>['required','string','max:1000','min:200'],
            'brand_type'=>['required','string','max:255',new Enum(BrandTypeEnum::class)],
            'logo'=>['required','mimes:jpg,jpeg,png,webp','max:'.config('app.image_size')],
        ];

        if(auth()->user()->isAdmin()){
            $validation['user_id']=['required','numeric','exists:users,id'];
            $validation['status']=['required','string','max:255',new Enum(StatusEnum::class)];
            if($this->get('status') == 'no'){
                $validation['reason_rejection']=['required','string','max:1000'];
            }
        }

        if($this->brand_type == 'ir'){
            if(!$this->hasFile('registration_form')){
                $validation['registration_form']=['nullable','mimes:jpg,jpeg,png,webp','max:'.config('app.image_size')];
                $validation['link']=['required','string','max:255'];
            }elseif(!empty($this->request->get('link'))){
                $validation['registration_form']=['required','mimes:jpg,jpeg,png,webp','max:'.config('app.image_size')];
                $validation['link']=['nullable','string','max:255'];
                if($this->isMethod('patch')){
                    $validation['registration_form'][0]='nullable';
                }
            }
        }else{
            $validation['link']=['required','string','max:255'];
        }

        if($this->isMethod('patch')){
            $validation['ir_name'][2]='unique:brands,id,name';
            $validation['en_name'][2]='unique:brands,id,en_name';
            $validation['logo'][0]='nullable';
        }

        return $validation;
    }
}
