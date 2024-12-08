<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required|string|max:255',   // usernameまたはemailでログイン可能
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'ユーザー名またはメールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }
}
