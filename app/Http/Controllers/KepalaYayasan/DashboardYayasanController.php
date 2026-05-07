<?php

namespace App\Http\Controllers\KepalaYayasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardYayasanController extends Controller
{
    public function index()
    {
        // 1. Dummy Metrik Eksekutif
        $metrics = [
            'terlambat_hari_ini' => 4,
            // Misal akumulasi keterlambatan bulan ini adalah 45 menit. (45 * Rp 8.000)
            'total_denda_bulan_ini' => 360000, 
            'rata_kehadiran' => 95, // Dalam persentase
        ];

        // 2. Dummy Data Grafik Tren Mingguan
        $chartData = [
            'labels' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
            'hadir' => [38, 40, 39, 37, 41, 39],
            'terlambat' => [5, 3, 4, 6, 2, 4],
            'alpa' => [2, 2, 2, 2, 2, 2] // Asumsi total guru 45
        ];

        $bulanSekarang = Carbon::now()->translatedFormat('F Y');

        return view('kepala-yayasan.dashboard', compact('metrics', 'chartData', 'bulanSekarang'));
    }
}