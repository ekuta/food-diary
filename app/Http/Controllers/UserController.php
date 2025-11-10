<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::create($credentials);
        event(new Registered($user));
        Auth::login($user);

        return $this->responseSuccess($user);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();
            return $this->responseSuccess($user);
        } else {
            return $this->responseFailure("メールアドレスまたはパスワードが正しくありません。");
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return $this->responseSuccess();
    }

    public function verificationNotification(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();
        return $this->responseSuccess();
    }

    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return $this->responseSuccess();
    }
}
