<?php

namespace App\Http\Controllers\Documentation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Documentation;
use App\Models\Teacher;


class DocumentationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|max:10240', // maks 10MB
        ]);

        // Simpan gambar
        $path = $request->file('image')->store('public/documentations');
        $fileUrl = Storage::url($path);

        // Ambil id_teacher dari login
        $nip = $request->input('nip'); // Dikirim dari frontend (localStorage)

        $teacher = Teacher::where('nip', $nip)->first();

        if (!$teacher) {
            return response()->json(['error' => 'Guru tidak ditemukan'], 401);
        }

        // Simpan ke DB
        $doc = new Documentation();
        $doc->uploaded_by = $teacher->name;
        $doc->id_teacher = $teacher->id_teacher;
        $doc->file_url = $fileUrl;
        $doc->id_class = 1; // (sementara hardcoded, nanti bisa dinamis)
        $doc->created_at = now();
        $doc->updated_at = now();
        $doc->save();

        return response()->json([
            'message' => 'Dokumentasi berhasil disimpan',
            'file_url' => $fileUrl
        ]);
    }
    
}