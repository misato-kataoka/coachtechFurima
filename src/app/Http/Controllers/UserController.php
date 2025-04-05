<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ContactRequest;

class UserController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }


    public function loginUser(Request $request){
        $credentials=$request->only('email', 'password');
        if(Auth::attempt($credentials)){
            return redirect('/');
        }
    }
}
