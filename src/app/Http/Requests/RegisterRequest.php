<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'username'=>['required'],
            'email'=>['required', 'email'],
            'password'=>['required', 'string', 'min:8'],
            'password_confirmation' => ['required','string','min:8','same:password'],
        ];
    }

    public function messages(){
        return [
            'username.required'=>'お名前を入力してください',
            'email.required'=>'メールアドレスを入力してください',
            'email.email'=>'メールアドレスは「ユーザー名＠ドメイン」形式で入力してください',
            'password.required'=>'パスワードを入力してください'
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password_confirmation.required' => 'パスワードの確認は必須です。',
            'password_confirmation.min' => 'パスワードの確認は8文字以上で入力してください。',
            'password_confirmation.same' => '確認用パスワードはパスワードと一致する必要があります。'
        ];
    }
}
