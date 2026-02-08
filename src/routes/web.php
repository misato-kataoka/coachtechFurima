<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\CommentController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


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
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/address/form', [RegisterController::class, 'showAddressForm'])->name('address.form'); // 認証済みユーザーのみ
    Route::post('/address', [RegisterController::class, 'storeAddress'])->name('address.store')/*->middleware('auth')*/; // 住所情報保存のルート
});

//ログアウト機能
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ログインページのルート
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginUser']);

// 商品一覧のルート
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/items', [ItemController::class, 'index'])->name('item.list');
});

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

/*
|--------------------------------------------------------------------------
| 決済関連のルート
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // 1. 商品購入ページの表示
    Route::get('/purchase/{item_id}', [OrdersController::class, 'show'])->name('purchase.show');

    // 2. Stripe Checkoutセッション作成 (JavaScriptから非同期で呼ばれる)
    Route::post('/create-checkout-session', [OrdersController::class, 'createCheckoutSession'])->name('checkout.create');

    // 3. 決済成功時のリダイレクト先
    Route::get('/payment/success', [OrdersController::class, 'success'])->name('payment.success');

    // 4. 決済キャンセル時のリダイレクト先
    Route::get('/payment/cancel', [OrdersController::class, 'cancel'])->name('payment.cancel');
});

// Stripe Webhook用のルート
Route::post('/stripe/webhook', [OrdersController::class, 'handleWebhook'])->name('stripe.webhook');

//住所変更のためのルート
Route::get('/purchase/address/{item_id}', [RegisterController::class, 'edit'])->name('address.edit')->middleware('auth');
Route::put('/purchase/address/update/{item_id}', [RegisterController::class, 'update'])->name('address.update')->middleware('auth');

// トップページルート
Route::get('/', [ItemController::class, 'index'])->name('home');
//Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('home');
//});

// マイページへのルート
Route::middleware(['auth', 'verified'])->group(function () {
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

// 1. 「メールを確認してください」画面を表示するためのルート
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// 2. ユーザーがメール内のリンクをクリックした時の処理ルート
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    // 認証が完了したら、商品一覧ページなどにリダイレクト
    return redirect('/items');
})->middleware(['auth', 'signed'])->name('verification.verify');

// 3. 認証メールを再送信するための処理ルート
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '新しい認証リンクを送信しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
