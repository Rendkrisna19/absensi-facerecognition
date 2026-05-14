<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use App\Models\LiburSemester; 
use App\Models\PengajuanIzin; // <-- JANGAN LUPA TAMBAHKAN INI
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Atur bahasa waktu ke Indonesia
        Carbon::setLocale('id');
        $hariIni = Carbon::now();
        $bulanIni = $hariIni->month;
        $tahunIni = $hariIni->year;
        $tanggalFormat = $hariIni->translatedFormat('l, d F Y');
        
        // 1. Cek apakah sudah absen HARI INI
        $absenHariIni = Absensi::where('user_id', $user->id)
                               ->where('tanggal', $hariIni->format('Y-m-d'))
                               ->first();

        // 2. CEK PENGAJUAN IZIN HARI INI
        // (Jika ada izin Pending/Disetujui yang mencakup hari ini)
        $izinHariIni = PengajuanIzin::where('user_id', $user->id)
                            ->whereDate('tanggal_mulai', '<=', $hariIni->format('Y-m-d'))
                            ->whereDate('tanggal_selesai', '>=', $hariIni->format('Y-m-d'))
                            ->whereIn('status', ['Pending', 'Disetujui'])
                            ->first();

        // 3. LOGIKA HARI LIBUR
        $isLibur = false;
        $keteranganLibur = '';

        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni->format('Y-m-d'))
                            ->whereDate('tanggal_selesai', '>=', $hariIni->format('Y-m-d'))
                            ->first();

        if ($liburSemester) {
            $isLibur = true;
            $keteranganLibur = 'Libur Semester: ' . $liburSemester->nama_semester;
        } elseif ($hariIni->isSunday()) {
            $isLibur = true;
            $keteranganLibur = 'Libur Akhir Pekan (Minggu)';
        } else {
            try {
                $response = Http::timeout(3)->get('https://dayoffapi.vercel.app/api?month=' . $hariIni->month . '&year=' . $hariIni->year);
                if ($response->successful()) {
                    $liburNasional = $response->json();
                    foreach ($liburNasional as $libur) {
                        if ($libur['tanggal'] === $hariIni->format('Y-m-d') && $libur['is_cuti'] === false) {
                            $isLibur = true;
                            $keteranganLibur = $libur['keterangan'];
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Abaikan jika API mati
            }
        }

        // 4. AMBIL DATA REAL UNTUK KARTU INFORMASI (Bulan Ini)
        $totalHadirBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', '!=', 'Alpa')
            ->count();

        $pengaturan = PengaturanAbsensi::first();
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

        $totalTelatBulanIni = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->where('status', 'Terlambat')
            ->count();

        $totalDendaBulanIni = $totalTelatBulanIni * $nominalDendaFlat;

        // 5. RIWAYAT PENGAJUAN IZIN (PAGINATION UNTUK DASHBOARD)
        $riwayatIzin = PengajuanIzin::where('user_id', $user->id)
                            ->orderBy('created_at', 'desc')
                            ->paginate(3); // Ditampilkan 3 per halaman agar tampilan HP tidak terlalu panjang

        return view('guru.dashboard.index', compact(
            'absenHariIni', 
            'izinHariIni',
            'tanggalFormat', 
            'isLibur', 
            'keteranganLibur',
            'totalHadirBulanIni',
            'totalDendaBulanIni',
            'riwayatIzin'
        ));
    }
}