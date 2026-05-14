<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('id');

        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);

        $riwayatAbsen = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString(); 

        $totalHadir = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->where('status', '!=', 'Alpa')
            ->count();

        return view('guru.riwayat.index', compact('riwayatAbsen', 'bulanSelected', 'tahunSelected', 'totalHadir'));
    }
}