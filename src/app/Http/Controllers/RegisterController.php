<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\AddressRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()  
    {  
        return view('auth.register');  
    }
    
    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();

        // ユーザー情報をセッションに保存
        session([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        // 住所入力フォームへのリダイレクト
        return redirect()->route('address.form');
    }

    public function showAddressForm()
    {

    // 会員登録フローに必要な情報がセッションに存在するか確認
        if (!session()->has('username') || !session()->has('email') || !session()->has('password')) {
    // 必要な情報がセッションになければ、登録の初期段階（会員登録フォーム）に戻す
        return redirect()->route('register')->with('error', '登録情報が不足しています。お手数ですが、最初からやり直してください。');
    }
        $user = null;
        $username_from_session = session('username'); // セッションからユーザー名を取得

        return view('auth.address', compact('user', 'username_from_session'));
    }

    public function storeAddress(AddressRequest $request)
    {
        \Log::info('storeAddress method called.');
    // セッションからユーザー情報を取得
        $username = session('username');
        $email = session('email');
        $password = session('password');

    // 画像のアップロード処理
        $profilePicPath = null;

        //if ($request->hasFile('image')) {
           // $profilePicPath = $request->file('image')->store('images', 'public');
           if ($request->hasFile('image')) {
            \Log::info('Image file is present in the request.'); // ファイルが存在するか確認
            try {
                $profilePicPath = $request->file('image')->store('images', 'public');
                \Log::info('Image stored. Path: ' . $profilePicPath); // 保存パスを確認
                if (!$profilePicPath) {
                    \Log::error('Failed to store image, store() returned falsy value.');
                }
            } catch (\Exception $e) {
                \Log::error('Exception during image store: ' . $e->getMessage()); // 例外が発生した場合
            }
        } else {
            \Log::info('No image file in the request.'); // ファイルが存在しない場合
        }
    

    // 新しいユーザーを作成
    try{
    $user = User::create([
        'username' => $username,
        'email' => $email,
        'password' => Hash::make($password),
        'post_code' => $request->post_code,
        'address' => $request->address,
        'building' => $request->building,
        'profile_pic' => $profilePicPath, // アップロードされた画像のパスを保存
    ]);
    \Log::info('User created successfully. User ID: ' . $user->id);
    } catch (\Exception $e) {
        \Log::error('Exception during user creation: ' . $e->getMessage());
    }

        // 完了後、ログイン
        Auth::login($user);

        // セッション情報をクリア
        session()->forget(['username', 'email', 'password']);

        // 完了メッセージと共にトップページにリダイレクト
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
    /* セッションからユーザー情報を取得
    $user = Auth::user();
    if (!$user) {
        return redirect('/login')->with('error', 'ログインしていません。');
    }

    // アドレス情報をユーザーに保存
    $user->address = $request->address;
    $user->post_code = $request->post_code;
    $user->building = $request->building;
    $user->save();

    // 完了後、トップ画面にリダイレクト
    return redirect('/')->with('success', '登録が完了しました。');
    }*/
