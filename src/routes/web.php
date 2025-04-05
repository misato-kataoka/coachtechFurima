<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

    //ユーザー登録のルート
    Route::post('/register', [AuthController::class, 'storeUser']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    //ログイン処理のルート
    Route::post('/login', [AuthController::class, 'loginUser']);
    // 住所確認ページのルート
    Route::get('/address', [RegisterController::class, 'address'])->name('address');
    //ログインページのルート
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

    Route::middleware('auth')->group(function () {
    
    });