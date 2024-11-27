<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 会員登録画面
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->withoutMiddleware(['auth']);
Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware(['auth']);
// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
// ログアウト機能
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    // 商品一覧画面
    Route::get('/', [ItemController::class, 'index'])->name('home');
    // 商品出品画面
    Route::get('/sell', [ItemController::class, 'showSell'])->name('show.sell');
    Route::post('/sell', [ItemController::class, 'createItem'])->name('sell');
    // 商品詳細画面
    Route::get('item/{id}', [ItemController::class, 'showDetail'])->name('item.detail');
    // 商品購入画面へ遷移
    Route::post('purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase');
    // 送付先住所変更画面
    Route::get('purchase/address/{item_id}', [PurchaseController::class, 'showAddressForm'])->name('show.purchase.address');
    Route::post('purchase/address/{item_id}', [PurchaseController::class, 'saveShippingAddress'])->name('change.purchase.address');
    // お気に入り機能
    Route::post('/items/{id}/favorite', [ItemController::class, 'toggleFavorite'])->name('item.favorite');
    // コメント送信フォーム
    Route::post('/items/{id}/comments', [ItemController::class, 'commentStore'])->name('comments.store');
    // プロフィール画面
    Route::get('/mypage', [UserController::class, 'showMypage'])->name('profile.mypage');
    // プロフィール編集画面
    Route::get('/mypage/profile', [UserController::class, 'showProfileForm'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'save'])->name('profile.save');
});

