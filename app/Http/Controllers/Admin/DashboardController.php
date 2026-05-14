<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Absensi; 
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();

        // 1. Hitung Total Guru
        $total_guru = User::where('role', 'guru')->count();

        // 2. Hitung Status Kehadiran Hari Ini
        // Sesuaikan nama kolom 'tanggal' dan 'status' dengan tabel database Anda
        $hadir = Absensi::whereDate('created_at', $hariIni)
                        ->where('status', 'tepat_waktu')
                        ->count();

        $terlambat = Absensi::whereDate('created_at', $hariIni)
                            ->where('status', 'terlambat')
                            ->count();

        // Alpa didapat dari Total Guru dikurangi yang sudah absen hari ini
        $total_sudah_absen = Absensi::whereDate('created_at', $hariIni)->distinct('user_id')->count('user_id');
        $alpa = $total_guru - $total_sudah_absen;
        // Pastikan tidak minus jika ada data anomali
        $alpa = $alpa < 0 ? 0 : $alpa; 

        $stats = [
            'total_guru' => $total_guru,
            'hadir'      => $hadir,
            'terlambat'  => $terlambat,
            'alpa'       => $alpa,
        ];

        // 3. Ambil 5 Aktivitas Absensi Terbaru Hari Ini
        $recent_absensi = Absensi::with('user') // Relasi ke model User
                            ->whereDate('created_at', $hariIni)
                            ->latest('jam_masuk') // Urutkan berdasarkan jam masuk terbaru
                            ->take(5)
                            ->get();

        return view('admin.dashboard.index', compact('stats', 'recent_absensi'));
    }
}