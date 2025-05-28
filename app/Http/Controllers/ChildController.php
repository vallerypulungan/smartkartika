<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Child;
use App\Models\ParentModel;
use App\Models\TahunAjaran;
use Illuminate\Support\Facades\DB;

class ChildController extends Controller
{
    // Tambah siswa & ortu
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'nis' => 'required|string|unique:children,nis',
            'tempatLahir' => 'required|string',
            'tanggalLahir' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'kelas' => 'required|exists:classes,id_class',
            'tahunAjaran' => 'required|exists:tahun_ajarans,id',
            // Data ortu
            'namaWali' => 'required|string',
            'email' => 'required|email',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
            // Akun
            'username' => 'required|string|unique:parents,username',
            'kode' => 'required|string|min:6',
        ]);

        // Simpan/ambil data ortu
        $parent = ParentModel::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->namaWali,
                'alamat' => $request->alamat,
                'num_telp' => $request->telepon,
                'password' => $request->kode,
                'username' => $request->telepon, // <-- ambil dari nomor telepon
            ]
        );

        // Simpan data siswa
        $child = Child::create([
            'name' => $request->nama,
            'nis' => $request->nis,
            'tempat_lahir' => $request->tempatLahir,
            'tanggal_lahir' => $request->tanggalLahir,
            'gender' => $request->gender,
            'id_class' => $request->kelas,
            'id_tahun_ajaran' => $request->tahunAjaran,
            'id_parent' => $parent->id_parent,
        ]);

        return response()->json(['message' => 'Siswa & Orang Tua berhasil ditambahkan', 'data' => $child]);
    }

    // Edit siswa & ortu
    public function update(Request $request, $id)
    {
        $child = Child::findOrFail($id);
        $parent = $child->parent;

        $request->validate([
            'nama' => 'required|string',
            'nis' => 'required|string|unique:children,nis,' . $child->id_child . ',id_child',
            'tempatLahir' => 'required|string',
            'tanggalLahir' => 'required|date',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'kelas' => 'required|exists:classes,id_class',
            'tahunAjaran' => 'required|exists:tahun_ajarans,id',
            // Data ortu
            'namaWali' => 'required|string',
            'email' => 'required|email',
            'alamat' => 'required|string',
            'telepon' => 'required|string',
            // Akun
            'username' => 'required|string|unique:parents,username,' . $parent->id_parent . ',id_parent',
            'kode' => 'required|string|min:6',
        ]);

        // Update ortu
        $parent->update([
            'name' => $request->namaWali,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'num_telp' => $request->telepon,
            'password' => $request->kode,
            'username' => $request->username,
        ]);

        // Update siswa
        $child->update([
            'name' => $request->nama,
            'nis' => $request->nis,
            'tempat_lahir' => $request->tempatLahir,
            'tanggal_lahir' => $request->tanggalLahir,
            'gender' => $request->gender,
            'id_class' => $request->kelas,
            'id_tahun_ajaran' => $request->tahunAjaran,
        ]);

        return response()->json(['message' => 'Siswa & Orang Tua berhasil diupdate', 'data' => $child]);
    }

    public function destroy($id)
    {
        $child = Child::findOrFail($id);

        \App\Models\Report::where('id_child', $child->id_child)->delete();

        $child->delete();

        return response()->json(['message' => 'Siswa berhasil dihapus']);
    }

    public function index(Request $request)
    {
        $query = Child::with(['parent', 'class', 'tahunAjaran']);

        if ($request->has('kelas')) {
            $query->where('id_class', $request->kelas);
        }
        if ($request->has('tahun')) {
            $query->where('id_tahun_ajaran', $request->tahun);
        }
        if ($request->has('parent')) {
            $query->where('id_parent', $request->parent);
        }

        $children = $query->get();
        return response()->json(['data' => $children]);
    }
    public function show($id)
    {
        $child = Child::with(['parent', 'class', 'tahunAjaran'])->findOrFail($id);
        return response()->json(['data' => $child]);
    }
}
