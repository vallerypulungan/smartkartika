<?php

namespace App\Http\Controllers;

use App\Models\ClassModel;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = ClassModel::select('id_class', 'class', 'class_level')->get();

        // Tambahkan "Lulus TK" jika belum ada di database
        

        // Urutkan: kelas biasa dulu, lalu "Lulus TK" paling kanan
        $sorted = $classes->sortBy(function ($item) {
            return $item->class === 'Lulus Tk' ? 999 : $item->id_class;
        })->values();

        return response()->json(['data' => $sorted]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'class' => 'required|string|max:255|unique:classes,class',
            'class_level' => 'required|string|max:10',
        ]);

        $class = ClassModel::create([
            'class' => $request->class,
            'class_level' => $request->class_level,
        ]);

        return response()->json(['message' => 'Kelas berhasil ditambahkan', 'data' => $class]);
    }

    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);

        $request->validate([
            'class' => 'required|string|max:255|unique:classes,class,' . $id . ',id_class',
            'class_level' => 'required|string|max:10',
        ]);

        $class->update([
            'class' => $request->class,
            'class_level' => $request->class_level,
        ]);

        return response()->json(['message' => 'Kelas berhasil diupdate', 'data' => $class]);
    }

    public function destroy($id)
    {
        $class = ClassModel::findOrFail($id);
        $class->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
