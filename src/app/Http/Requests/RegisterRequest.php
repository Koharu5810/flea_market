<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Psy\CodeCleaner\ReturnTypePass;

class RegisterRequest extends FormRequest
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
            'username' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:8|confirmed',  // confirmedルールで確認用と一致か確認
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password.confirmed' => 'パスワードと一致しません',
        ];
    }
}
