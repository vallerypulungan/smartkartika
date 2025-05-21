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
        if (!$classes->contains('class', 'Lulus TK')) {
            $classes->push((object)[
                'id_class' => 4,
                'class' => 'Lulus TK',
                'class_level' => 'F',
            ]);
        }

        return response()->json(['data' => $classes]);
    }
}
