<?php

namespace App\Http\Controllers\Documentation;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Documentation;
use App\Models\Teacher;


class DocumentationController extends Controller
{
    public function index()
    {
        $docs = \App\Models\Documentation::select('id_document', 'title', 'description', 'file_url', 'uploaded_by', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $docs]);
    }
    public function update(Request $request, $id)
{
    try {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|image|max:10240',
        ]);

        $doc = Documentation::findOrFail($id);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($doc->file_url && Storage::exists(str_replace('storage/', 'public/', $doc->file_url))) {
                Storage::delete(str_replace('storage/', 'public/', $doc->file_url));
            }

            $originalName = $request->file('image')->getClientOriginalName();
            $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
            $path = $request->file('image')->storeAs(
            'public/documentations',
            $cleanedName
            );
            $doc->file_url = 'storage/documentations/' . $cleanedName;
        }

        $doc->title = $request->title;
        $doc->description = $request->description;
        $doc->save();

        return response()->json(['message' => 'Berhasil diupdate']);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}





    public function destroy($id)
    {
        $doc = Documentation::findOrFail($id);
        $doc->delete();

        return response()->json(['message' => 'Berita berhasil dihapus']);
    }


    public function store(Request $request)
{
    try {
        $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|image|max:10240',
        ]);

        $originalName = $request->file('image')->getClientOriginalName();
        $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);

        $path = $request->file('image')->storeAs('documentations', $cleanedName, 'public');
        // Gunakan path URL yang cocok dengan public/storage
        $fileUrl = url(Storage::url($path)); 
        $teacher = Teacher::where('nip', $request->input('nip'))->first();
        if (!$teacher) return response()->json(['error' => 'Guru tidak ditemukan'], 401);

        $doc = new Documentation();
        $doc->uploaded_by = $teacher->name;
        $doc->id_teacher = $teacher->id_teacher;
        $doc->title = $request->input('title');
        $doc->description = $request->input('description');
        $doc->file_url = $fileUrl;
        $doc->save();

        return response()->json([
            'message' => 'Dokumentasi berhasil disimpan',
            'file_url' => $fileUrl
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    
}