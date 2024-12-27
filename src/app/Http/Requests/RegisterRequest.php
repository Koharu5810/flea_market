<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\PasswordMatch;

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
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'password_confirmation' => [
                'required',
                'string',
                new PasswordMatch($this->input('password')),
            ],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'お名前を入力してください',
            'username.unique' => '既に登録されているユーザー名です',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique' => '既に登録されているメールアドレスです',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password_confirmation.required' => '確認用パスワードを入力してください',
            // パスワード不一致エラーメッセージはApp\Rules\PasswordMatchで定義
        ];
    }
}
