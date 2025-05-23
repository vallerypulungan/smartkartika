<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Child;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    // Upload laporan
    public function store(Request $request)
    {
        $request->validate([
            'id_teacher' => 'required|exists:teachers,id_teacher',
            'id_class' => 'required|exists:classes,id_class',
            'id_parent' => 'required|exists:parents,id_parent',
            'id_child' => 'required|exists:children,id_child',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $originalName = $request->file('file')->getClientOriginalName();
        $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
        $path = $request->file('file')->storeAs('laporan', $cleanedName, 'public');
        $fileUrl = url(Storage::url($path)); // hasil: http://localhost:8000/storage/laporan/namafile.pdf

        $report = Report::create([
            'id_teacher' => $request->id_teacher,
            'id_class' => $request->id_class,
            'id_parent' => $request->id_parent,
            'id_child' => $request->id_child,
            'file' => $fileUrl,
        ]);

        return response()->json(['message' => 'Laporan berhasil diupload', 'data' => $report]);
    }

    // List semua laporan
    public function index()
    {
        $laporan = Report::with(['child', 'parent', 'teacher', 'class'])->get();
        return response()->json(['data' => $laporan]);
    }

    // Laporan berdasarkan anak
    public function byChild($id_child)
    {
        $laporan = Report::with(['teacher', 'class', 'parent'])
            ->where('id_child', $id_child)
            ->get();
        return response()->json(['data' => $laporan]);
    }

    // Laporan berdasarkan ortu
    public function byParent($id_parent)
    {
        $laporan = Report::with(['teacher', 'class', 'child'])
            ->where('id_parent', $id_parent)
            ->get();
        return response()->json(['data' => $laporan]);
    }
}
