<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'username' => ['required','string'],
            'postal_code' => ['required','string','regex:/^\d{3}-\d{4}$/'],
            'address' => ['required'],
            'building_name' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'お名前を入力してください。',
            'postal_code.required' => '郵便番号を入力してください。',
            'postal_code.regex' => '郵便番号はハイフンありの8文字で入力してください。例: 123-4567',
            'address.required' => '住所を入力してください。',
            'building_name.required' => '建物名を入力してください。',
        ];
    }
}
