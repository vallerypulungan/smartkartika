<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
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
                'redirect' => '/profil'
            ]);
        }

        // Kalau salah
        return response()->json([
            'status' => 'fail',
            'message' => 'Username atau Password salah'
        ], 401);
    }
    public function logoutApi(Request $request)
    {
        // Kalau kamu simpan token nanti, bisa revoke di sini
        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }

}
