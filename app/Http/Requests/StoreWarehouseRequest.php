<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'min:1', 'max:255'],
            'address' => ['required', 'string', 'min:1', 'max:1024'],
            'supplier_id' => ['required', 'integer']
        ];
    }

//    public function attributes()
//    {
//        return [
//            'supplier_id' => 'supplier'
//        ];
//    }
}
