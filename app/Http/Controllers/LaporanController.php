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
        $laporan = Report::with(['child.tahunAjaran', 'parent', 'teacher', 'class'])->get();
        return response()->json(['data' => $laporan]);
    }

    // Laporan berdasarkan anak
    public function byChild($id_child)
    {
        $laporan = Report::with(['teacher', 'class', 'parent', 'child.tahunAjaran'])
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

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        // Validasi: semua field boleh nullable, kecuali file tetap dicek jika ada
        $request->validate([
            'id_teacher' => 'nullable|exists:teachers,id_teacher',
            'id_class' => 'nullable|exists:classes,id_class',
            'id_parent' => 'nullable|exists:parents,id_parent',
            'id_child' => 'nullable|exists:children,id_child',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        // Siapkan data update, gunakan data lama jika tidak dikirim
        $data = [
            'id_teacher' => $request->input('id_teacher', $report->id_teacher),
            'id_class' => $request->input('id_class', $report->id_class),
            'id_parent' => $request->input('id_parent', $report->id_parent),
            'id_child' => $request->input('id_child', $report->id_child),
        ];

        // Jika ada file baru, hapus file lama dan upload file baru
        if ($request->hasFile('file')) {
            if ($report->file) {
                $oldPath = str_replace(url('/storage'), 'public', $report->file);
                Storage::delete($oldPath);
            }
            $originalName = $request->file('file')->getClientOriginalName();
            $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
            $path = $request->file('file')->storeAs('laporan', $cleanedName, 'public');
            $fileUrl = url(Storage::url($path));
            $data['file'] = $fileUrl;
        } else {
            // Jika tidak ada file baru, tetap gunakan file lama
            $data['file'] = $report->file;
        }

        $report->update($data);

        return response()->json(['message' => 'Laporan berhasil diupdate', 'data' => $report->fresh()]);
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        // Hapus file dari storage
        if ($report->file) {
            $oldPath = str_replace(url('/storage'), 'public', $report->file);
            Storage::delete($oldPath);
        }

        $report->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus']);
    }
}
