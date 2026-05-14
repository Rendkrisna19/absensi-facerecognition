<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengajuanIzinController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin guru (Riwayat Pengajuan)
     */
    public function index()
    {
        $pengajuanIzins = PengajuanIzin::where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->paginate(10);
                            
        return view('guru.pengajuan_izin.index', compact('pengajuanIzins'));
    }

    /**
     * Menampilkan form untuk mengajukan izin baru
     */
    public function create()
    {
        return view('guru.pengajuan_izin.create');
    }

    /**
     * Menyimpan data pengajuan izin ke database
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:Sakit,Izin,Cuti',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'required|string|max:1000',
            'file_bukti' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048', // Maks 2MB
        ]);

        $pathBukti = null;
        if ($request->hasFile('file_bukti')) {
            // Simpan di folder storage/app/public/bukti_izin agar aman
            $pathBukti = $request->file('file_bukti')->store('bukti_izin', 'public');
        }

        PengajuanIzin::create([
            'user_id' => auth()->id(),
            'jenis' => $request->jenis,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'alasan' => $request->alasan,
            'file_bukti' => $pathBukti,
            'status' => 'Pending', // Default status
        ]);

        return redirect()->route('guru.pengajuan-izin.index')
                         ->with('success', 'Pengajuan ' . $request->jenis . ' berhasil dikirim dan menunggu persetujuan.');
    }

    /**
     * Menampilkan detail satu pengajuan (Opsional jika ingin dibuatkan view khusus)
     */
    public function show(PengajuanIzin $pengajuanIzin)
    {
        // Pastikan hanya bisa melihat miliknya sendiri
        abort_if($pengajuanIzin->user_id !== auth()->id(), 403);
        
        return view('guru.pengajuan_izin.show', compact('pengajuanIzin'));
    }
    
    // Fitur Edit & Destroy bisa Anda biarkan kosong jika aturan sekolah 
    // tidak mengizinkan edit/hapus setelah pengajuan dikirim.
}