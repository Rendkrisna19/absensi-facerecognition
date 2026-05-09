<?php

namespace App\Http\Controllers\KepalaYayasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use App\Models\PengaturanAbsensi;
use Carbon\Carbon;

class DashboardYayasanController extends Controller
{
    public function index(Request $request)
    {
        // Set bahasa kalender ke Indonesia
        Carbon::setLocale('id');
        $hariIni = Carbon::now()->format('Y-m-d');
        
        // 1. FILTER BULAN & TAHUN (Default ke bulan ini jika tidak difilter)
        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);

        // 2. MENGAMBIL DATA GURU (Perbaikan error 'Call to undefined method role()')
        // Pastikan nama kolom hak aksesnya benar, di sini saya asumsikan 'role'
        $totalGuru = User::where('role', 'guru')->count();

        // 3. METRIK EKSEKUTIF HARI INI
        $absenHariIni = Absensi::where('tanggal', $hariIni)->get();
        $hadirTepat = $absenHariIni->where('status', 'Hadir')->count();
        $terlambatHariIni = $absenHariIni->where('status', 'Terlambat')->count();
        
        $sudahAbsen = $hadirTepat + $terlambatHariIni;
        $alpaHariIni = $totalGuru > 0 ? ($totalGuru - $sudahAbsen) : 0;

        // 4. METRIK DENDA BULAN INI (Berdasarkan Filter)
        $pengaturan = PengaturanAbsensi::first();
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;
        
        $totalTelatBulanIni = Absensi::whereMonth('tanggal', $bulanSelected)
                                     ->whereYear('tanggal', $tahunSelected)
                                     ->where('status', 'Terlambat')
                                     ->count();
                                     
        $totalDendaBulanIni = $totalTelatBulanIni * $nominalDendaFlat;

        // Rata-rata kehadiran hari ini (Persentase)
        $rataKehadiran = $totalGuru > 0 ? round(($sudahAbsen / $totalGuru) * 100) : 0;

        // Kumpulkan data Metrics untuk dikirim ke View (sesuai format kamu sebelumnya)
        $metrics = [
            'total_guru' => $totalGuru,
            'hadir_tepat' => $hadirTepat,
            'terlambat_hari_ini' => $terlambatHariIni,
            'alpa_hari_ini' => $alpaHariIni,
            'total_denda_bulan_ini' => $totalDendaBulanIni,
            'rata_kehadiran' => $rataKehadiran,
        ];

        // 5. DATA 5 ABSEN TERAKHIR HARI INI
        $absenTerakhir = Absensi::with('user')
            ->where('tanggal', $hariIni)
            ->orderBy('jam_masuk', 'desc')
            ->take(5)
            ->get();

        // 6. GRAFIK TREN MINGGUAN (7 Hari Terakhir - Data Asli)
        $labelHari = [];
        $dataHadir = [];
        $dataTelat = [];
        $dataAlpa = [];

        for ($i = 6; $i >= 0; $i--) {
            // Mundur dari 6 hari lalu sampai hari ini (0)
            $tgl = Carbon::now()->subDays($i);
            $labelHari[] = $tgl->translatedFormat('D'); // Menghasilkan: Sen, Sel, Rab, dll

            $absenHarian = Absensi::where('tanggal', $tgl->format('Y-m-d'))->get();
            $h = $absenHarian->where('status', 'Hadir')->count();
            $t = $absenHarian->where('status', 'Terlambat')->count();
            $a = $totalGuru > 0 ? ($totalGuru - ($h + $t)) : 0;

            $dataHadir[] = $h;
            $dataTelat[] = $t;
            $dataAlpa[] = $a;
        }

        $chartData = [
            'labels' => $labelHari,
            'hadir' => $dataHadir,
            'terlambat' => $dataTelat,
            'alpa' => $dataAlpa
        ];

        $bulanSekarang = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->translatedFormat('F Y');

        // Pastikan folder view kamu benar: resources/views/kepala-yayasan/dashboard.blade.php
        return view('kepala-yayasan.dashboard', compact(
            'metrics', 
            'chartData', 
            'bulanSekarang', 
            'absenTerakhir', 
            'bulanSelected', 
            'tahunSelected'
        ));
    }
}