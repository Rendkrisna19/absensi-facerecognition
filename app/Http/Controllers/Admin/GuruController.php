<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Guru;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Exports\GuruExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class GuruController extends Controller
{
   public function index()
{
   
    $gurus = User::with('guru')
                 ->where('role', 'guru') 
                 ->orderBy('name', 'asc') // Default sorting
                 ->get();

    return view('admin.guru.index', compact('gurus'));
}

    public function create()
    {
        return view('admin.guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:users,nik',
            'password' => 'required|min:6',
            'jabatan' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'required|numeric',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Validasi foto
        ]);

        try {
            // Handle upload foto sebelum masuk transaksi DB
            $fotoPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoPath = $request->file('foto_profil')->store('profil', 'public');
            }

            DB::transaction(function () use ($request, $fotoPath) {
                // 1. Simpan ke tabel Users
                $user = User::create([
                    'name' => $request->name,
                    'username' => $request->nik, // Default username disamakan dengan NIK
                    'nik' => $request->nik,
                    'password' => Hash::make($request->password),
                    'role' => 'guru',
                    'jabatan' => $request->jabatan,
                    'foto_profil' => $fotoPath, // Simpan path foto
                ]);

                // 2. Simpan ke tabel Gurus (Biodata)
                Guru::create([
                    'user_id' => $user->id,
                    'tempat_lahir' => $request->tempat_lahir,
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'jenis_kelamin' => $request->jenis_kelamin,
                    'agama' => $request->agama,
                    'alamat' => $request->alamat,
                    'no_hp' => $request->no_hp,
                    'pendidikan_terakhir' => $request->pendidikan_terakhir,
                    'status_pegawai' => $request->status_pegawai,
                    'tanggal_bergabung' => $request->tanggal_bergabung,
                ]);
            });

            return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(User $guru)
    {
        // Pastikan yang diedit benar-benar memiliki role guru
        if ($guru->role !== 'guru') {
            abort(404, 'Data guru tidak ditemukan');
        }

        // Muat relasi biodata (tabel gurus)
        $guru->load('guru');
        
        return view('admin.guru.edit', compact('guru'));
    }

    public function update(Request $request, User $guru)
    {
        // Validasi NIK dikecualikan untuk ID user yang sedang diedit
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|numeric|unique:users,nik,' . $guru->id,
            'password' => 'nullable|min:6', // Boleh kosong (nullable) jika password tidak ingin diubah
            'jabatan' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp' => 'required|numeric',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Validasi foto
        ]);

        try {
            DB::transaction(function () use ($request, $guru) {
                // 1. Siapkan data update untuk tabel Users
                $userData = [
                    'name' => $request->name,
                    'username' => $request->nik, // Username ngikut update NIK jika NIK diganti
                    'nik' => $request->nik,
                    'jabatan' => $request->jabatan,
                ];

                // Jika kolom password diisi, maka update passwordnya
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }

                // Handle upload foto baru dan hapus foto lama
                if ($request->hasFile('foto_profil')) {
                    // Hapus foto lama jika ada di storage
                    if ($guru->foto_profil && Storage::disk('public')->exists($guru->foto_profil)) {
                        Storage::disk('public')->delete($guru->foto_profil);
                    }
                    // Simpan foto baru
                    $userData['foto_profil'] = $request->file('foto_profil')->store('profil', 'public');
                }

                // Eksekusi update tabel users
                $guru->update($userData);

                // 2. Update atau Create ke tabel Gurus (Biodata)
                // Menggunakan updateOrCreate agar jika data biodata guru ini sebelumnya belum ada, sistem akan otomatis membuatnya.
                Guru::updateOrCreate(
                    ['user_id' => $guru->id],
                    [
                        'tempat_lahir' => $request->tempat_lahir,
                        'tanggal_lahir' => $request->tanggal_lahir,
                        'jenis_kelamin' => $request->jenis_kelamin,
                        'agama' => $request->agama,
                        'alamat' => $request->alamat,
                        'no_hp' => $request->no_hp,
                        'pendidikan_terakhir' => $request->pendidikan_terakhir,
                        'status_pegawai' => $request->status_pegawai,
                        'tanggal_bergabung' => $request->tanggal_bergabung,
                    ]
                );
            });

            return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }
    }
    
    public function destroy(User $guru)
    {
        try {
            // Hapus foto profil dari storage agar tidak menjadi sampah server
            if ($guru->foto_profil && Storage::disk('public')->exists($guru->foto_profil)) {
                Storage::disk('public')->delete($guru->foto_profil);
            }

            $guru->delete(); // Karena cascade, biodata di tabel gurus otomatis terhapus
            return redirect()->route('admin.guru.index')->with('success', 'Data Guru berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    //function untuk export all
    // Method Export Excel
    public function exportExcel()
    {
        return Excel::download(new GuruExport, 'Data_Pegawai_Tri_Jaya.xlsx');
    }

    // Method Export PDF (Semua Data)
    public function exportPdf()
    {
        $gurus = User::with('guru')->where('role', 'guru')->orderBy('name', 'asc')->get();
        
        // Load view dengan library PDF
        $pdf = Pdf::loadView('admin.guru.pdf', compact('gurus'))->setPaper('a4', 'landscape');
        return $pdf->download('Data_Pegawai_Tri_Jaya.pdf');
    }

    // Method Print/PDF (Per Guru)
    public function print(User $guru)
    {
        if ($guru->role !== 'guru') {
            abort(404, 'Data tidak ditemukan');
        }

        $guru->load('guru');
        
        // Kita gunakan stream agar PDF langsung terbuka di tab baru (bisa diprint langsung)
        $pdf = Pdf::loadView('admin.guru.print', compact('guru'))->setPaper('a4', 'portrait');
        return $pdf->stream('Profil_Pegawai_' . $guru->name . '.pdf');
    }
}