<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


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

// // 会員登録画面
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
    // 商品詳細画面
    Route::get('item/{id}', [ItemController::class, 'show'])->name('item.detail');
    // お気に入り機能
    Route::post('/favorite/{item}', [ItemController::class, 'toggle'])->name('favorite.toggle');
    // コメント送信フォーム
    Route::post('/comments', [ItemController::class, 'commentStore'])->name('comments.store');
    // プロフィール画面
    Route::get('/mypage', [UserController::class, 'showMypage'])->name('profile.mypage');
    // プロフィール編集画面
    Route::get('/mypage/profile', [UserController::class, 'showProfileForm'])->name('profile.edit');
    Route::post('/mypage/profile', [UserController::class, 'save'])->name('profile.save');
});

