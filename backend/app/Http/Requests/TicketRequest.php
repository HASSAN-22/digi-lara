<?php

namespace App\Http\Requests;

use App\Enums\IsLockedEnum;
use App\Enums\PriorityEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TicketRequest extends FormRequest
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
            'ticketcategory_id'=>['required','numeric','exists:ticketcategories,id'],
            'title'=>['required','string','max:255'],
            'message'=>['required','string','max:10000'],
            'priority'=>['required','string','max:7',new Enum(PriorityEnum::class)],
        ];
    }
}
