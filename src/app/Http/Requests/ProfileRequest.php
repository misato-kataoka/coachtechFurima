<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'profile_image' => 'required|file|mimes:jpeg,png|max:2048', // プロフィール画像は必須で、jpegまたはpng形式、2MG以下
        ];
    }

    public function messages()
    {
        return [
            'profile_image.required' => 'プロフィール画像は必須です。',
            'profile_image.mimes' => 'プロフィール画像はjpegまたはpng形式でアップロードしてください。',
            'profile_image.max' => 'プロフィール画像のサイズは2MB以下でなければなりません。',
        ];
    }
}
