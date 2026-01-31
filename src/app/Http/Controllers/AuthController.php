<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
//use App\Actions\Fortify\CreateNewUser;

class AuthController extends Controller
{
    protected $creator;//use App\Actions\Fortify\CreateNewUserと繋がる。protected=外に出さない共有変数、$creator処理を委譲する相手

    public function register()
    {
        return view('user.register');
    }

    public function store(RegisterRequest $request)
    {
        $user = $this->creator->create($request->all());
        $user->sendEmailVerificationNotification();
        return redirect('/register')->with('message','登録が完了しました。認証メールを送信しましたのでご確認ください。');
    }
}
