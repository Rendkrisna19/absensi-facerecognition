<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('guru.pengaturan.index', compact('user'));
    }

    public function updateProfil(Request $request)
    {
        $request->validate([
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' 
        ]);

        $user = auth()->user();

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                Storage::delete('public/' . $user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profil', 'public');
            $user->update(['foto_profil' => $path]);
        }

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }
}