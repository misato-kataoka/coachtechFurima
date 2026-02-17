<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChatRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // ルートから渡された 'item' を使って、出品者か購入者かを確認する
        $item = $this->route('item');
        
        // ログインユーザーが商品の出品者でも購入者でもない場合は false を返す
        if ($this->user()->id != $item->user_id && $this->user()->id != $item->buyer_id) {
            return false;
        }

        // 権限があれば true を返す
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return[
            'message' => 'required|string|max:400',
            'image'   => 'nullable|image|mimes:jpeg,png|max:2048', 
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max'      => '本文は400文字以内で入力してください',

            'image.mimes'      => '「.png」または「.jpeg」形式でアップロードしてください',
            'image.image'      => '画像ファイルを選択してください',
        ];
    }
}
