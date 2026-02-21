<?php

use Illuminate\Support\Facades\Route; // ルーティング機能を使う
use App\Http\Controllers\UserController; //一般ユーザー用の処理
use App\Http\Controllers\AdminController; //管理者用の処理
use App\Http\Controllers\AuthController; //ログイン・ログアウト・登録処理
use App\Http\Controllers\MiddlewareController;
use App\Http\Middleware\AdminStatusMiddleware; //管理者かどうかを判定するミドルウェア
// use Laravel\Fortify\Http\Controller\VerifyEmailController; //メール認証用
use Illuminate\Http\Request; //HTTPリクエスト情報
use App\Http\Requests\CorrectionRequest; //勤怠修正申請用のバリデーション


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

Route::middleware('auth')->group(function(){ //ログインしている人だけがアクセスできる
    Route::get('/attendance', [UserController::class, 'index']); //勤怠打刻画面を表示する
    Route::post('/attendance', [UserController::class, 'attendance']);//出退勤ボタンを押した時の処理
    Route::get('/attendance/list', [UserController::class, 'list']);//勤怠一覧画面を表示
    Route::get('/attendance/{id}', [UserController::class, 'detail']);//仮：勤怠詳細画面表示
    Route::post('/attendance/{id}', [UserController::class,'detail']);//仮：勤怠詳細画面処理
    Route::post('/attendance/{id}', [UserCOntroller::class, 'amendmentApplication']);//仮：勤怠修正
    Route::get('/application/{id}', [UserController::class, 'applicationDetail']);//勤怠修正申請の詳細を表示
    Route::get('/stamp_correction_request/list', [UserController::class, 'applicationList']);//仮：申請一覧画面表示
    
});

Route::middleware(['auth'])->group(function(){//管理者向け（ログイン必須）
    //管理画面系
    Route::get('/admin/attendance/list', [AdminController::class, 'list']);//全スタッフの勤怠一覧
    // Route::get('/admin/staff/list', [AdminController::class, 'staffList']);//スタッフ一覧
    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'staffDetailList']);//特定スタッフの勤怠詳細
    Route::post('/admin/logout', [AdminController::class, 'adminLogout']);//管理者ログアウト
    // //勤怠修正申請の承認
    // Route::get('/stamp_correction_request/approve/{id}',[AdminController::class, 'approvalShow']);//修正申請の承認画面表示
    // Route::post('/stamp_correction_request/approve/{id}', [AdminController::class, 'approval']);//修正申請を承認する処理
    // Route::post('/export', [AdminController::class, 'export']);//勤怠データをCSVなどでエクスポート
});


//管理者か一般ユーザーかで処理を切り替えるルート
Route::middleware(['auth', AdminStatusMiddleware::class])->group(function(){//ログイン必須、管理者フラグをチェック
    Route::get('/stamp_correction_request/list', function(Request $request){//直前の画面が/adminから来ていて
        if($request->headers->has('referer') && str_contains($request->headers->get('referer'), '/admin')){
            if(auth()->user()->admin_status){//かつ管理者なら→adminController
                return app(AdminController::class)->applicationList($request);
            }
        }else {//それ以外→UserController
            return app(UserController::class)->applicationList($request);
        }
    });
    Route::get('/attendance/{id}',function($id, Request $request){//勤怠詳細画面（共通URL)
        if($request->headers->has('referer') && str_contains($request->headers->get('referer'), 'admin')){
            if(auth()->user()->admin_status){//管理画面から来た管理者→管理者用詳細
                return app(AdminController::class)->detail($id);
            }
        } else{//一般ユーザー→自分の勤怠詳細
            return app(UserController::class)->detail($id);
        }
    });
    Route::post('/attendance/{id}', function (CorrectionRequest $request, $id){//勤怠修正申請の送信
        if(auth()->user()->admin_status){//管理者→管理者用修正処理
            if(auth()->user()->admin_status){
                return app(AdminController::class)->amendmentApplication($request, $id);
            }
        } else {//一般ユーザー→ユーザー用修正申請処理
            return app(UserController::class)->amendmentApplication($request, $id);
        }
    });
});

Route::get('/admin/login', [AuthController::class, 'adminLogin']);//管理者ログイン画面
Route::post('/admin/login', [AuthController::class, 'adminDoLogin']);//管理者ログイン処理

Route::get('/login',[AuthController::class, 'userLogin']);
Route::post('/login', [AuthController::class, 'doLogin']);//一般ユーザーログイン
Route::post('/logout', [AuthController::class, 'doLogout']);//ログアウト
Route::get('/register',[AuthController::class,'register']);
Route::post('/register', [AuthController::class, 'store']);//会員登録
// Route::get('/email/verify', function(){//メール認証を促す画面表示
//     return view('auth.verify-email');
// })->middleware(['auth'])->name('verification.notice');
// Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])//メール内リンクを踏んだ時の認証処理
//     ->middleware(['signed'])
//     ->name('verification.verify');


