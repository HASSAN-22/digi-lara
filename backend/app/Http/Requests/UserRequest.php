<?php

namespace App\Http\Requests;

use App\Enums\StatusEnum;
use App\Enums\UserAccessEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UserRequest extends FormRequest
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
            'name'=>['required','string','max:255'],
            'access'=>['required','string','max:255', new Enum(UserAccessEnum::class)],
            'mobile'=>['required','numeric','digits:11,11','unique:users,id'],
            'password'=>['required','string','max:255','min:8'],
            'status'=>['required','string','max:255', new Enum(StatusEnum::class)],
            'avatar'=>['nullable','mimes:jpg,jpeg,png,webp',"max:".config('app.image_size')],
            'role'=>['nullable','numeric','exists:roles,id']
        ];

        if($this->isMethod('PATCH')){
            $validation['mobile'][3] = 'unique:users,id,mobile';
            $validation['password'][0] = 'nullable';
        }
        return $validation;
    }
}
