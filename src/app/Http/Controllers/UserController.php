<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function update(AddressRequest $request)
    {
        // 認証されたユーザーを取得
        $user = Auth::user();

        // ユーザー情報を更新
        $user->username = $request->username;
        $user->postal_code = $request->postal_code;
        $user->address = $request->address;
        $user->building_name = $request->building_name;

        // プロフィール画像がアップロードされた場合
        if ($request->hasFile('imageUpload')) {
            $imagePath = $request->file('imageUpload')->store('profiles', 'public');
            $user->profile_picture = $imagePath;
        }

        // データベースに保存
        $user->save();

        // mypage.blade.php にリダイレクト
        return redirect()->route('mypage')->with('success', 'プロフィールが更新されました！');
    }

    public function show()
    {
        $user = Auth::user(); // 現在ログインしているユーザーを取得

        $purchasedItems = Order::where('buyer_id', $user->id)->paginate(8);

        // ビューにユーザー情報を渡す
        return view('mypage', compact('user', 'purchasedItems'));
    }

    public function profile()  
    {  
    $purchasedItems = Order::where('buyer_id', Auth::id())->paginate(8); // 購入商品  
    $listedItems = Order::where('seller_id', Auth::id())->paginate(8); // 出品商品  

    return view('profile', compact('listedItems', 'purchasedItems'));  
    }  

}

