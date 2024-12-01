<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use FontLib\Table\Type\name;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 会員登録画面
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->withoutMiddleware(['auth']);
Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware(['auth']);
// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('show.login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
// ログアウト機能
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
// トップページ表示
Route::get('/', [ItemController::class, 'index'])->name('home');
// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'showDetail'])->name('item.detail');
// 検索機能
Route::get('/search', [ItemController::class, 'search'])->name('search');

Route::middleware('auth')->group(function () {
    // お気に入り機能
    Route::post('/item/{item_id}/favorite', [ItemController::class, 'toggleFavorite'])->name('item.favorite');
    // コメント送信フォーム
    Route::post('/item/{item_id}/comments', [ItemController::class, 'commentStore'])->name('comments.store');
    // 商品購入画面へ遷移
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');
    // 商品購入実行
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'checkout'])->name('purchase.checkout');
    Route::get('/purchase/{item_id}/success', [PurchaseController::class, 'success'])->name('purchase.success');
    // 送付先住所変更画面
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('edit.purchase.address');
    Route::patch('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('update.purchase.address');

    // 商品出品画面
    Route::get('/sell', [ItemController::class, 'showSell'])->name('show.sell');
    Route::post('/sell', [ItemController::class, 'createItem'])->name('sell');
    // プロフィール画面
    Route::get('/mypage', [UserController::class, 'showMypage'])->name('profile.mypage');
    // プロフィール編集画面
    Route::get('/mypage/profile', [UserController::class, 'showProfileForm'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'save'])->name('profile.save');
});

