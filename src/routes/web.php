<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\AuthController;
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

// 会員登録画面
Route::get('/register', [AuthController::class, 'showRegistrationForm']);
// ログイン画面
Route::get('/login', [AuthController::class, 'showLoginForm']);

// Fortify認証
Route::middleware('auth')->group(function () {
    Route::get('/', [ItemController::class, 'index']);
});
