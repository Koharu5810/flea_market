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

// アクション作成前一時表示用
// Route::get('register', function() {
//     return view('auth.register');
// });

// // 会員登録画面
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->withoutMiddleware(['auth']);
Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware(['auth']);
// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::middleware('auth')->group(function () {
    // 商品一覧画面
    Route::get('/', [ItemController::class, 'index'])->name('home');
    // プロフィール画面
    Route::get('/mypage', [UserController::class, 'index'])->name('profile.index');
});

