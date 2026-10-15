<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $global_config = (object) get_global_setting();

        return view('auth.login', compact('global_config'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $authCredentials = [
            'username' => $credentials['email'],
            'password' => $credentials['password'],
            'active' => 1,
        ];

        if (Auth::attempt($authCredentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            Auth::user()->forceFill(['last_login' => now()])->save();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => translate('username_password_incorrect'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
