<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class UserController extends Controller
{

    public function loginUser(LoginRequest $request){
        $credentials=$request->only('email','password');
        if(Auth::attempt($credentials)){
            return redirect('/attendance');
        }

        return redirect()->back()
        ->withInput($request->only('email'))
        ->withErrors([
            'email' =>'ログイン情報が登録されていません。',
        ]);
    }
    public function showLoginForm()
    {
        return view('auth.login');

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors([
                'password' => 'パスワードは8文字以上で以上で入力してください',
            ]);

        return redirect()->back()
            ->withInput($request->only('email'))
            ->withErrors([
                'password.confirmation' => 'パスワードと一致しません',
            ]);
    }

    public function storeUser(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        Auth::login($user);
        return redirect('/');

    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function logout(){
        return view('auth.login');
    }
}
