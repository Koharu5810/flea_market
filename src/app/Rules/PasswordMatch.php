<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordMatch implements Rule
{
    private $password;

    public function __construct($password)
    {
        $this->password = $password;
    }

    public function passes($attribute, $value)
    {
        // 会員登録時の確認用パスワードがパスワードと一致するかチェック
        return $value === $this->password;
    }

    public function message()
    {
        return 'パスワードが一致しません';
    }
}
