<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'required|string|in:コンビニ支払い,カード支払い',
            'postal_code' => 'required|string',
            'address' => 'required|string',
            'building' => 'required|string',
        ];
    }
    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'postal_code.required' => '配送先を登録してください',
            'address.required' => '配送先を登録してください',
            'building.required' => '配送先を登録してください',
        ];
    }
}
