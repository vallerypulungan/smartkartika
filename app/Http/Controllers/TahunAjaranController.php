<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunAjaran;

class TahunAjaranController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:tahun_ajarans,nama',
        ]);

        $tahun = TahunAjaran::create([
            'nama' => $request->nama,
        ]);

        return response()->json(['message' => 'Tahun ajaran berhasil ditambahkan', 'data' => $tahun]);
    }

    public function index()
    {
        return response()->json(['data' => TahunAjaran::all()]);
    }
}
