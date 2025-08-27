<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

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

//ログイン処理
Route::post('/login',[UserController::class,'loginUser']);
//ログイン画面表示
//Route::get('/login',[UserController::class, 'showLoginForm']);

//会員登録処理
Route::post('/register',[UserController::class, 'storeUser']);
//会員登録画面
Route::get('/register',[UserController::class,'showRegisterForm']);


//Route::get('/attendance', [AuthController::class, 'index']);
Route::middleware(['auth','verified'])->group(function(){
    Route::get('/attendance',[AuthController::class,'index']);
});