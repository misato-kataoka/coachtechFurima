<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
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

//ユーザー登録のルート
    Route::post('/register', [AuthController::class, 'storeUser']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
//ログイン処理のルート
    Route::post('/login', [AuthController::class, 'loginUser']);
// 住所確認ページのルート
    Route::get('/address', [RegisterController::class, 'address'])->name('address');
//ログインページのルート
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');
// mypage へのルート
Route::get('/mypage', [UserController::class, 'show'])->name('mypage');

Route::get('/email/verify', [EmailVerificationNoticeController::class, '__invoke'])
    ->middleware(['auth', 'verified'])
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['auth', 'signed']) // 認証済み、署名付きであることをチェック
    ->name('verification.verify');

Route::post('/email/verification-notification', function () {
    return 'メール再送信の処理（サンプル）'; // 再送信の処理を実装
})->middleware(['auth'])->name('verification.resend');

    Route::middleware('auth')->group(function () {
    
    });