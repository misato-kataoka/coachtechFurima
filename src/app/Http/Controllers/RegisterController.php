<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use App\Models\User; // Userモデルをインポート
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(AddressRequest $request)
    {
        $validatedData = $request->validated();

        // ユーザー登録
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 登録後に住所をセッションに保存
        session(['address' => $validatedData['address']]);

        // 登録後にaddressメソッドを呼び出してリダイレクト
        return $this->address();
    }

    public function address() // 新しいメソッドを追加
    {
        return view('address'); // address.blade.phpを表示
    }
}