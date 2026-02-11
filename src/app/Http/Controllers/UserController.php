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

    public function store(AddressRequest $request)
    {
        // 認証されたユーザーを取得
        $user = Auth::user();

        // ユーザー情報を新規登録
        $user->username = $request->username;
        $user->post_code = $request->post_code;
        $user->address = $request->address;
        $user->building = $request->building;

        // プロフィール画像がアップロードされた場合
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
            $user->profile_pic = $imagePath;
        }

        $user->save();

        return redirect()->route('mypage')->with('message', 'プロフィールを登録しました！');
    }

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
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
            $user->profile_pic = $imagePath;
        }

        $user->save();

        return redirect()->route('mypage')->with('message', 'プロフィールが更新されました！');
    }

    public function edit()
    {
        $user = Auth::user();

        return view('auth.address', compact('user'));
    }

    public function show(Request $request)
    {
        $user = Auth::user(); // 現在の認証ユーザーを取得

        $listedItems = Item::where('user_id', $user->id)
                        ->latest()
                        ->simplePaginate(8);

    $purchasedItems = Item::where('buyer_id', $user->id)
                            ->latest()
                            ->simplePaginate(8);

    $activeTab = $request->query('tab', 'sell');

    if ($activeTab === 'buy') {
        $itemsToShow = $purchasedItems;
    } else {
        $itemsToShow = $listedItems;
    }

    return view('mypage', [
        'user' => $user,
        'itemsToShow' => $itemsToShow,
        'activeTab' => $activeTab,
    ]);
    }


    public function profile()
    {
        $purchasedItems = Item::where('buyer_id', Auth::id())->paginate(8); // 購入商品
        $soldItems = Item::where('user_id', $user->id)->paginate(8); // 出品商品

        return view('profile', compact('soldItems', 'purchasedItems'));
    }

}

