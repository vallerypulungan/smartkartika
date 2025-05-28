<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\ParentModel;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    public function loginApi(Request $request)
    {
        $credentials = $request->only('username', 'password');

        // Cek sebagai Guru
        $guru = Teacher::where('nip', $credentials['username'])->first();
        if ($guru && $credentials['password'] === $guru->password) {
            return response()->json([
                'status' => 'success',
                'role' => 'guru',
                'name' => $guru->name,
                'email' => $guru->email,
                'num_telp' => $guru->num_telp,
                'nip' => $guru->nip,
                'message' => 'Login Guru berhasil',
                'redirect' => '/dashboard-guru',
            ]);
        }

        // Cek sebagai Orang Tua
        $ortu = ParentModel::where('num_telp', $credentials['username'])->first();
        if ($ortu && $credentials['password'] === $ortu->password) {
            return response()->json([
                'status' => 'success',
                'role' => 'ortu',
                'id_parent' => $ortu->id_parent,
                'name' => $ortu->name,
                'email' => $ortu->email,
                'num_telp' => $ortu->num_telp,
                'alamat' => $ortu->alamat,
                'username' => $ortu->username,
                'message' => 'Login Orang Tua berhasil',
                'redirect' => '/dashboardortu',
            ]);
        }

        return response()->json([
            'status' => 'fail',
            'message' => 'Username atau Password salah'
        ], 401);
    }

    public function logoutApi(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Logout berhasil'
        ]);
    }

}
