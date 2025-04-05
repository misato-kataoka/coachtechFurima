<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'product_name' => ['required'],
            'product_description' => ['required','string','max:255'], // 商品説明は必須、最大255文字
            'product_image' => ['required','file','mimes:jpeg,png','max:2048'], // 商品画像はアップロード必須、jpegまたはpng
            'category_id' => ['required','integer'], // 商品のカテゴリーは選択必須、カテゴリーモデルのIDが存在すること
            'condition_id' => ['required','integer'], // 商品の状態は選択必須
            'price' => ['required','numeric','min:0'], // 商品価格は必須、数値型、0円以上
        ];
    }

    public function messages()
    {
        return [
            'product_name.required' => '商品名を入力してください。',
            'product_description.required' => '商品説明を入力してください。',
            'product_description.max' => '商品説明は255文字以内で入力してください。',
            'product_image.required' => '商品画像はアップロード必須です。',
            'product_image.mimes' => '商品画像はjpegまたはpng形式でアップロードしてください。',
            'product_image.max' => '商品画像のサイズは2MB以下でなければなりません。',
            'category_id.required' => '商品のカテゴリーは選択必須です。',
            'condition_id.required' => '商品の状態は選択必須です。',
            'price.required' => '商品価格を入力してください。',
            'price.numeric' => '商品価格は数値である必要があります。',
            'price.min' => '商品価格は0円以上でなければなりません。',
        ];
    }
}
