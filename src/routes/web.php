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

// ユーザー登録のルートAuth->Registerに変更
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register.form');
Route::post('/register', [RegisterController::class, /*'storeUser'*/'register'])->name('register');

// 住所入力ページへのルート
Route::get('/address', [RegisterController::class, 'showAddressForm'])->name('address.form')->middleware('auth'); // 認証済みユーザーのみ
Route::post('/address', [RegisterController::class, 'storeAddress'])->name('address.store')->middleware('auth'); // 住所情報保存のルート

//ログアウト機能
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ログインページのルート
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginUser']);

// 商品一覧のルート（GETリクエスト）  
Route::get('/items', [ItemController::class, 'index'])->name('item.list');

//商品検索のルート
Route::get('/item/search', [ItemController::class, 'search'])->name('item.search');

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

/*
// メール確認関連のルート
Route::get('/email/verify', [EmailVerificationNoticeController::class, '__invoke'])
    ->middleware(['auth', 'verified'])
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verify'])
    ->middleware(['auth', 'signed']) // 認証済み、署名付きであることをチェック
    ->name('verification.verify');

Route::post('/email/verification-notification', function () {
    return 'メール再送信の処理（サンプル）'; // 再送信の処理を実装
})->middleware(['auth'])->name('verification.resend');*/
});