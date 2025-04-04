<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    public function storeUser(RegisterRequest $request){
        $user=User::create([
            'username'=>$request->username,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        event(new Registered($user));
        Auth::login($user);
        return redirect()->route('verification.notice');
        //return redirect('/address');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function loginUser(LoginRequest $request){
        $credentials=$request->only('email', 'password');
        if(Auth::attempt($credentials)){
            return redirect()->intended('/');
        }
    }

    public function logout()
    {
        Auth::logout(); // ログアウト処理
        return redirect('auth.login'); // ログインページへリダイレクト
    }

    // メール認証の処理
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect('/address')->with('verified', 'メールアドレスが認証されました。');
    }
}
