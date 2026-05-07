<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FaceRecordingController extends Controller
{
    // Menampilkan daftar guru yang bisa direkam wajahnya
    public function index()
    {
        $gurus = User::where('role', 'guru')->latest()->get();
        return view('admin.face.index', compact('gurus'));
    }

    // Menampilkan antarmuka kamera untuk merekam wajah guru tertentu
    public function record(User $guru)
    {
        if ($guru->role !== 'guru') {
            abort(404, 'Data bukan guru.');
        }
        return view('admin.face.record', compact('guru'));
    }

    // Menyimpan data array titik wajah (descriptor) ke database via AJAX
    public function store(Request $request, User $guru)
    {
        $request->validate([
            'face_descriptor' => 'required|string'
        ]);

        try {
            $guru->update([
                'face_descriptor' => $request->face_descriptor
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Wajah berhasil direkam dan disimpan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan wajah: ' . $e->getMessage()
            ], 500);
        }
    }
}