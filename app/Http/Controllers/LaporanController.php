<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Child;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
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
        
        $report = Report::create([
            'id_teacher' => $request->id_teacher,
            'id_class' => $request->id_class,
            'id_parent' => $request->id_parent,
            'id_child' => $request->id_child,
            'file' => 'laporan/' . $cleanedName,
        ]);

        return response()->json(['message' => 'Laporan berhasil diupload', 'data' => $report]);
    }

    public function index()
    {
        $laporan = Report::with(['child.tahunAjaran', 'parent', 'teacher', 'class'])->get();
        $laporan->transform(function ($item) {
            $item->file = asset('storage/' . $item->file);
            return $item;
        });
        return response()->json(['data' => $laporan]);
    }

    public function byChild($id_child)
    {
        $laporan = Report::with(['teacher', 'class', 'parent', 'child.tahunAjaran'])
            ->where('id_child', $id_child)
            ->get();
        $laporan->transform(function ($item) {
            $item->file = asset('storage/' . $item->file);
            return $item;
        });
        return response()->json(['data' => $laporan]);
    }

    public function byParent($id_parent)
    {
        $laporan = Report::with(['teacher', 'class', 'child'])
            ->where('id_parent', $id_parent)
            ->get();
        $laporan->transform(function ($item) {
            $item->file = asset('storage/' . $item->file);
            return $item;
        });
        return response()->json(['data' => $laporan]);
    }

    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $request->validate([
            'id_teacher' => 'nullable|exists:teachers,id_teacher',
            'id_class' => 'nullable|exists:classes,id_class',
            'id_parent' => 'nullable|exists:parents,id_parent',
            'id_child' => 'nullable|exists:children,id_child',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $data = [
            'id_teacher' => $request->input('id_teacher', $report->id_teacher),
            'id_class' => $request->input('id_class', $report->id_class),
            'id_parent' => $request->input('id_parent', $report->id_parent),
            'id_child' => $request->input('id_child', $report->id_child),
        ];

        if ($request->hasFile('file')) {
            if ($report->file) {
                $oldPath = str_replace(url('/storage'), 'public', $report->file);
                Storage::delete($oldPath);
            }
            $originalName = $request->file('file')->getClientOriginalName();
            $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
            $path = $request->file('file')->storeAs('laporan', $cleanedName, 'public');
            $data['file'] = 'laporan/' . $cleanedName;
        } else {
            $data['file'] = $report->file;
        }

        $report->update($data);
        return response()->json(['message' => 'Laporan berhasil diupdate', 'data' => $report->fresh()]);
    }

    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        if ($report->file) {
            $oldPath = str_replace(url('/storage'), 'public', $report->file);
            Storage::delete($oldPath);
        }

        $report->delete();

        return response()->json(['message' => 'Laporan berhasil dihapus']);
    }

    public function download($filename)
    {
        return response()->download(storage_path('app/public/' . $filename));
    }
}
