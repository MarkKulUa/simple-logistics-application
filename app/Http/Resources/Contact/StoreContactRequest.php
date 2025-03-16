<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'first_name' => 'string|nullable',
            'last_name' => 'string|nullable',
            'company_name' => 'string|nullable',
            'email' => 'nullable|email:rfc|required_without_all:first_name,last_name,company_name|unique:contacts,email,NULL,id,deleted_at,NULL',
            'phone' => 'string|nullable',
            'notes' => 'nullable',
            'tags' => 'nullable|array',
        ];
    }
}
