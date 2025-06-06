<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\CommentController;
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
Route::get('/address/form', [RegisterController::class, 'showAddressForm'])->name('address.form'); // 認証済みユーザーのみ
Route::post('/address', [RegisterController::class, 'storeAddress'])->name('address.store')/*->middleware('auth')*/; // 住所情報保存のルート

//ログアウト機能
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ログインページのルート
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginUser']);

// 商品一覧のルート
Route::get('/items', [ItemController::class, 'index'])->name('item.list');

//商品検索のルート
Route::get('/item/search', [ItemController::class, 'search'])->name('item.search');

// 商品詳細のルート
Route::get('/item/{id}', [ItemController::class, 'show'])->name('item.detail');

// マイリストに追加するためのルート
Route::middleware('auth')->group(function () {
    Route::get('/mylist', [ItemController::class, 'myList'])->name('item.mylist');
});


//いいね機能に関するルート
Route::post('/item/{id}/like', [ItemController::class, 'like'])->name('item.like');

//コメントを保存するためのルート
Route::middleware(['auth'])->group(function () {
    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
});

//商品購入画面へのルート
Route::get('/purchase/{item_id}', [OrdersController::class, 'show'])->middleware('auth')->name('purchase.show');
Route::post('/orders/store', [OrdersController::class, 'store'])->name('orders.store');

//商品購入時のルート
Route::post('/create-checkout-session', [OrdersController::class, 'createCheckoutSession'])->name('create.checkout.session');
Route::get('/success', function () {return view('success');
})->name('success.page');

Route::get('/cancel', function () {
    return view('cancel');
})->name('cancel.page');

//住所変更のためのルート
Route::get('/purchase/address/{item_id}', [RegisterController::class, 'edit'])->name('address.edit')->middleware('auth');
Route::put('/purchase/address/update/{item_id}', [RegisterController::class, 'update'])->name('address.update')->middleware('auth');

// トップページルート
Route::get('/', [ItemController::class, 'index'])->name('home');
//Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('home');
//});

// マイページへのルート
Route::middleware('auth')->group(function () {
    Route::get('/mypage', [UserController::class, 'show'])->name('mypage');
    Route::post('/mypage/profile', [UserController::class, 'update'])->name('profile.update');
    Route::get('/mypage/profile/edit', [UserController::class, 'edit'])->name('address.edit'); // プロフィール編集
    Route::post('/mypage/profile/update', [UserController::class, 'update'])->name('address.update'); // プロフィール更新
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile'); // プロフィールページ
});

//商品登録のルート
Route::middleware(['auth'])->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('items.index'); // 商品リスト
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store'); // 商品の作成処理
    Route::get('/items/{id}', [ItemController::class, 'show'])->name('items.show'); // 商品詳細
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
})->middleware(['auth'])->name('verification.resend');
});*/