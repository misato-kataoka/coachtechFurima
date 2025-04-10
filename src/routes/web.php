<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use Illuminate\Foundation\Auth\EmailVerificationNoticeController;


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

// ユーザー登録のルート
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'storeUser']);

// ログインページのルート
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginUser']);

// 商品詳細のルート 
Route::get('/item/{id}', [ItemController::class, 'show'])->name('item.detail');

// マイページへのルート
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserController::class, 'show'])->name('mypage');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');

// トップページルート
    Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('home');
});

// 住所確認ページのルート（認証なしの場合）
Route::get('/address', [RegisterController::class, 'address'])->name('address');

// メール確認関連のルート
Route::get('/email/verify', [EmailVerificationNoticeController::class, '__invoke'])
    ->middleware(['auth', 'verified'])
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['auth', 'signed']) // 認証済み、署名付きであることをチェック
    ->name('verification.verify');

Route::post('/email/verification-notification', function () {
    return 'メール再送信の処理（サンプル）'; // 再送信の処理を実装
})->middleware(['auth'])->name('verification.resend');
});