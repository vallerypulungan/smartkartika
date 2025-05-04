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
        try {
            $request->validate([
                'title' => 'required|string',
                'description' => 'required|string',
                'image' => 'required|image|max:10240',
            ]);

            $path = $request->file('image')->storeAs(
                'public/documentations',
                time() . '_' . $request->file('image')->getClientOriginalName()
            );
            if ($request->hasFile('file')) {
                $path = $request->file('file')->store('uploads', 'public');
                return response()->json(['message' => 'File uploaded successfully', 'path' => $path]);
            }    
            $fileUrl = Storage::url($path);

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