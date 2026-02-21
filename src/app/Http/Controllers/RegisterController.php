<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        //session([
        $user = User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        event(new Registered($user));

        Auth::login($user);

        // 認証メールを送ったことをユーザーに知らせるページへリダイレクト
        return redirect()->route('verification.notice');
    }

    public function showAddressForm()
    {
        $user = Auth::user();
        $username_from_session = $user->username;

        return view('auth.address', compact('user', 'username_from_session'));
    }

    public function storeAddress(AddressRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            // 万が一ユーザーが取得できない場合は、ログインページに戻す
            return redirect()->route('login')->with('error', 'エラーが発生しました。再度ログインしてください。');
        }

    // 画像のアップロード処理
        $profilePicPath = null;

            if ($request->hasFile('image')) {
            \Log::info('Image file is present in the request.'); // ファイルが存在するか確認
            try {
                $profilePicPath = $request->file('image')->store('images', 'public');
                \Log::info('Image stored. Path: ' . $profilePicPath); // 保存パスを確認
                if (!$profilePicPath) {
                    \Log::error('Failed to store image, store() returned falsy value.');
                }
            } catch (\Exception $e) {
                \Log::error('Exception during image store: ' . $e->getMessage());
            }
        } else {
            \Log::info('No image file in the request.');
        }

    // ユーザー情報を「更新 (update)」する
    $user->update([
        'post_code' => $request->post_code,
        'address' => $request->address,
        'building' => $request->building,
        'profile_pic' => $profilePicPath,
    ]);

        return redirect('/mypage')->with('success', '登録が完了しました。');
    }

    public function edit($item_id)
    {
        $user = Auth::user();

        return view('auth.address', compact('user', 'item_id'));
    }

    public function update(Request $request, $item_id)
    {
        $user = Auth::user();
        $user->username = $request->input('username');
        $user->post_code = $request->input('post_code');
        $user->address = $request->input('address');
        $user->building = $request->input('building');
        $user->save();

        return redirect()->route('purchase.index')->with('success', '住所が更新されました。');
    }
}

