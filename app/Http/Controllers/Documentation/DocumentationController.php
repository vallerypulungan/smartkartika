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
        $docs = Documentation::select('id_document', 'title', 'description', 'file_url', 'uploaded_by', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $docs->transform(function ($doc) {
            $doc->file_url = asset('storage/' . $doc->file_url);
            return $doc;
        });

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

        $title = $request->input('title', $request->json('title'));
        $description = $request->input('description', $request->json('description'));
        $nip = $request->input('nip', $request->json('nip'));
        $teacher = Teacher::where('nip', $nip)->first();
        if (!$teacher) return response()->json(['error' => 'Guru tidak ditemukan'], 401);

        $doc = Documentation::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($doc->file_url && Storage::exists('public/' . $doc->file_url)) {
                Storage::delete('public/' . $doc->file_url);
            }

            $originalName = $request->file('image')->getClientOriginalName();
            $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
            $path = $request->file('image')->storeAs('documentations', $cleanedName, 'public');
            $doc->file_url = 'documentations/' . $cleanedName; 
        }

        $doc->title = $title;
        $doc->description = $description;
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

        $title = $request->input('title', $request->json('title'));
        $description = $request->input('description', $request->json('description'));
        $nip = $request->input('nip', $request->json('nip'));
        $teacher = Teacher::where('nip', $nip)->first();
        if (!$teacher) return response()->json(['error' => 'Guru tidak ditemukan'], 401);

        $originalName = $request->file('image')->getClientOriginalName();
        $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);

        $path = $request->file('image')->storeAs('documentations', $cleanedName, 'public');

        $doc = new Documentation();
        $doc->uploaded_by = $teacher->name;
        $doc->id_teacher = $teacher->id_teacher;
        $doc->title = $title;
        $doc->description = $description;
        $doc->file_url = 'documentations/' . $cleanedName;
        $doc->save();

        return response()->json([
            'message' => 'Dokumentasi berhasil disimpan',
            'file_url' => asset('storage/' . $doc->file_url)
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    
}