<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Untuk login dari web biasa (Blade)
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

    // Untuk login dari Android Studio atau Vue Frontend via API
    public function loginApi(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Cek akun guru
        if ($credentials['username'] === 'iniguru' && $credentials['password'] === 'iniguru') {
            return response()->json([
                'status' => 'success',
                'role' => 'guru',
                'message' => 'Login Guru berhasil',
                'redirect' => '/dashboard-guru'
            ]);
        }

        // Cek akun orang tua
        if ($credentials['username'] === 'iniwalimurid' && $credentials['password'] === 'iniwalimurid') {
            return response()->json([
                'status' => 'success',
                'role' => 'ortu',
                'message' => 'Login Orang Tua berhasil',
                'redirect' => '/dashboard-ortu'
            ]);
        }

        // Kalau salah
        return response()->json([
            'status' => 'fail',
            'message' => 'Username atau Password salah'
        ], 401);
    }
}
