<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    // Menampilkan daftar guru
    public function index()
    {
        $teachers = Teacher::select('id_teacher', 'name', 'nip', 'email', 'num_telp', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $teachers]);
    }

    // Menyimpan data guru baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:30',
            'nip' => 'required|string|unique:teachers,nip',
            'email' => 'required|email|unique:teachers,email',
            'num_telp' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $teacher = Teacher::create([
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'num_telp' => $request->num_telp,
            'password' => Hash::make($request->password), // hash password
        ]);

        return response()->json(['message' => 'Guru berhasil ditambahkan', 'data' => $teacher]);
    }

    // Mengupdate data guru
    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'required|string|unique:teachers,nip,' . $teacher->id_teacher . ',id_teacher',
            'email' => 'required|email|unique:teachers,email,' . $teacher->id_teacher . ',id_teacher',
            'num_telp' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $updateData = [
            'name' => $request->name,
            'nip' => $request->nip,
            'email' => $request->email,
            'num_telp' => $request->num_telp,
        ];
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password); // hash password
        }
        $teacher->update($updateData);

        return response()->json(['message' => 'Guru berhasil diupdate', 'data' => $teacher]);
    }

    // Menghapus data guru
    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();

        return response()->json(['message' => 'Guru berhasil dihapus']);
    }
}
