<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // file blade form login
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password', 'kode');

        if ($credentials['kode'] === 'guru') {
            if (Auth::guard('teacher')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                return redirect()->intended('/dashboard-guru');
            }
        }

        if ($credentials['kode'] === 'ortu') {
            if (Auth::guard('parent')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                return redirect()->intended('/dashboard-ortu');
            }
        }

        return back()->withErrors(['msg' => 'Login gagal. Cek email, password, dan kode.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/login');
    }
}
