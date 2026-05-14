<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengajuanIzin;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\CarbonPeriod;

class PengajuanIzinController extends Controller
{
    public function index()
    {
        // Mengambil semua pengajuan izin beserta data user (guru/pegawai)
        $pengajuanIzins = PengajuanIzin::with('user')->orderBy('created_at', 'desc')->get();

        // Menghitung statistik untuk Cards
        $totalPending = $pengajuanIzins->where('status', 'Pending')->count();
        $totalDisetujui = $pengajuanIzins->where('status', 'Disetujui')->count();
        $totalDitolak = $pengajuanIzins->where('status', 'Ditolak')->count();

        return view('admin.pengajuan_izin.index', compact('pengajuanIzins', 'totalPending', 'totalDisetujui', 'totalDitolak'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Disetujui,Ditolak',
            'catatan_penolakan' => 'nullable|string'
        ]);

        $izin = PengajuanIzin::findOrFail($id);
        $izin->status = $request->status;
        $izin->catatan_penolakan = $request->status === 'Ditolak' ? $request->catatan_penolakan : null;
        $izin->disetujui_oleh = auth()->id();
        $izin->direspon_pada = now();
        $izin->save();

        // LOGIKA PENTING: Jika disetujui, otomatis generate status absen (Sakit/Izin/Cuti) ke tabel absensis
        if ($request->status === 'Disetujui') {
            $period = CarbonPeriod::create($izin->tanggal_mulai, $izin->tanggal_selesai);
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                
                // Gunakan firstOrCreate agar jika tanggal tersebut sudah ada data (misal alpa), tidak duplikat,
                // Namun jika Anda ingin menimpa data Alpa menjadi Sakit, gunakan updateOrCreate:
                Absensi::updateOrCreate(
                    ['user_id' => $izin->user_id, 'tanggal' => $dateStr],
                    [
                        'status' => $izin->jenis, // 'Sakit', 'Izin', atau 'Cuti'
                        'jam_masuk' => null,
                        'menit_terlambat' => 0
                    ]
                );
            }
        }

        return redirect()->back()->with('success', 'Status pengajuan izin berhasil diperbarui!');
    }
}