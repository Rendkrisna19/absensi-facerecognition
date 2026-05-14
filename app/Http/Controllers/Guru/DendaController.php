<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\PengaturanAbsensi;
use Carbon\Carbon;

class DendaController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('id');

        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);

        $pengaturan = PengaturanAbsensi::first();
        $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

        $riwayatTerlambat = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->where('status', 'Terlambat')
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalHariTelat = $riwayatTerlambat->count();
        $totalDenda = $totalHariTelat * $nominalDendaFlat;

        $namaBulanTahun = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->translatedFormat('F Y');

        return view('guru.denda.index', compact(
            'riwayatTerlambat', 
            'totalHariTelat', 
            'totalDenda', 
            'nominalDendaFlat',
            'bulanSelected',
            'tahunSelected',
            'namaBulanTahun'
        ));
    }
}