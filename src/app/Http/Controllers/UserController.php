<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\ContactRequest;
use App\Http\Requests\AddressRequest;

class UserController extends Controller
{

    public function update(AddressRequest $request)
    {
        // 認証されたユーザーを取得
        $user = Auth::user();

        // ユーザー情報を更新
        $user->username = $request->username;
        $user->post_code = $request->post_code;
        $user->address = $request->address;
        $user->building = $request->building;

        // プロフィール画像がアップロードされた場合
        if ($request->hasFile('imageUpload')) {
            $imagePath = $request->file('imageUpload')->store('profiles', 'public');
            $user->profile_pic = $imagePath;
        }

        $user->save();

        return redirect()->route('mypage')->with('success', 'プロフィールが更新されました！');
    }

    public function edit()
    {
        $user = Auth::user();

        return view('auth.address', compact('user'));
    }

    public function show(Request $request)
    {
        $user = Auth::user(); // 現在の認証ユーザーを取得

        $purchasedItems = Item::where('buyer_id', $user->id)->paginate(8); // 購入した商品リスト
        $soldItems = Item::where('user_id', $user->id)->paginate(8); // 出品した商品リスト

        $activeTab = $request->query('tab', 'sell');
        if ($activeTab === 'buy') {
            $itemsToShow = $purchasedItems; // 購入した商品
        } else {
            $itemsToShow = $soldItems; // 出品した商品
        }

        return view('mypage', compact('user', 'itemsToShow', 'activeTab'));
    }


    public function profile()
    {
        $purchasedItems = Item::where('buyer_id', Auth::id())->paginate(8); // 購入商品
        $soldItems = Item::where('user_id', $user->id)->paginate(8); // 出品商品

        return view('profile', compact('soldItems', 'purchasedItems'));
    }

}

