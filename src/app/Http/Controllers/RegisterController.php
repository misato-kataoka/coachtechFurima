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

        // ユーザー登録
        //$user = User::create([
        // ユーザー情報をセッションに保存  
        session([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);

        // 住所入力フォームへのリダイレクト  
        return redirect()->route('address.form');  
    }

        // 登録後に住所をセッションに保存
        //session(['address' => $validatedData['address']]);

        // 登録後にaddressメソッドを呼び出してリダイレクト
        //return $this->address();
    //}

    public function showAddressForm()  
    {  
        return view('auth.address'); // address.blade.phpを表示  
    }

    //public function address() //住所登録ページへ遷移
    //{
        //return view('address'); // address.blade.phpを表示
    //}

    public function storeAddress(AddressRequest $request)
    {

    // セッションからユーザー情報を取得  
        $username = session('username');  
        $email = session('email');  
        $password = session('password');  

        // 画像のアップロード処理  
    $profilePicPath = null;

    if ($request->hasFile('profile_pic')) {  
        $profilePicPath = $request->file('profile_pic')->store('profile_pics', 'public'); // 'profile_pics' ディレクトリに保存  
    }

        // 新しいユーザーを作成  
        $user = User::create([  
            'username' => $username,  
            'email' => $email,  
            'password' => Hash::make($password),  
            'post_code' => $request->post_code,  
            'address' => $request->address,  
            'building' => $request->building,  
            'profile_pic' => $profilePicPath, // アップロードされた画像のパスを保存
        ]);  

        // 完了後、ログイン  
        Auth::login($user);  

        // セッション情報をクリア  
        session()->forget(['username', 'email', 'password']);  

        // 完了メッセージと共にトップページにリダイレクト  
        return redirect('/')->with('success', '登録が完了しました。');  
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
