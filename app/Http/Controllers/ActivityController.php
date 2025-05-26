<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Facades\Storage;
use App\Models\Teacher;


class ActivityController extends Controller
{
    public function index()
    {
        $activities = Activity::orderBy('created_at', 'desc')->get();
        // Mapping agar image_url menjadi URL publik
        $activities->transform(function ($item) {
            $item->image_url = $item->image_url ? asset('storage/' . $item->image_url) : null;
            return $item;
        });
        return response()->json(['data' => $activities]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'activity_tittle' => 'required|string',
                'description' => 'required|string',
                'date' => 'required|date',
                'image' => 'nullable|image|max:10240',
                'nip' => 'required|string',
            ]);

            // Cari guru berdasarkan NIP
            $teacher = Teacher::where('nip', $request->input('nip'))->first();
            if (!$teacher) return response()->json(['error' => 'Guru tidak ditemukan'], 401);

            $activity = new Activity();
            $activity->activity_tittle = $request->input('activity_tittle');
            $activity->description = $request->input('description');
            $activity->date = $request->input('date');
            $activity->id_teacher = $teacher->id_teacher; // ambil dari hasil pencarian guru

            if ($request->hasFile('image')) {
                $originalName = $request->file('image')->getClientOriginalName();
                $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
                $path = $request->file('image')->storeAs('activities', $cleanedName, 'public');
                // Simpan hanya relative path
                $activity->image_url = 'activities/' . $cleanedName;
            }

            $activity->save();

            return response()->json(['message' => 'Kegiatan berhasil ditambahkan']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);

        $request->validate([
            'activity_tittle' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
            'image' => 'nullable|image|max:10240',
        ]);
        
        $teacher = Teacher::where('nip', $request->input('nip'))->first();
        if (!$teacher) return response()->json(['error' => 'Guru tidak ditemukan'], 401);


        $activity->activity_tittle = $request->activity_tittle;
        $activity->description = $request->description;
        $activity->date = $request->date;

        if ($request->hasFile('image')) {
            if ($activity->image_url && Storage::exists('public/' . $activity->image_url)) {
                Storage::delete('public/' . $activity->image_url);
            }
            $originalName = $request->file('image')->getClientOriginalName();
            $cleanedName = time() . '_' . preg_replace('/[^A-Za-z0-9.\-_]/', '_', $originalName);
            $path = $request->file('image')->storeAs('activities', $cleanedName, 'public');
            $activity->image_url = 'activities/' . $cleanedName;
            }

        $activity->save();

        return response()->json(['message' => 'Kegiatan berhasil diupdate']);
    }

    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        if ($activity->image_url && Storage::exists(str_replace('storage/', 'public/', $activity->image_url))) {
            Storage::delete(str_replace('storage/', 'public/', $activity->image_url));
        }
        $activity->delete();

        return response()->json(['message' => 'Kegiatan berhasil dihapus']);
    }
}
