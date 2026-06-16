<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Services\AlpaService;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        Carbon::setLocale('id');

        $bulanSelected = $request->input('bulan', Carbon::now()->month);
        $tahunSelected = $request->input('tahun', Carbon::now()->year);

        // Auto-create Alpa record for today before loading riwayat
        AlpaService::createAlpaRecords(Carbon::now()->format('Y-m-d'));

        // Backfill past working days in selected month that are missing Alpa records
        $startOfMonth = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->startOfMonth()->format('Y-m-d');
        $endOfRange = Carbon::now()->subDay()->format('Y-m-d');
        if ($startOfMonth < $endOfRange) {
            AlpaService::backfillAlpaRecords($startOfMonth, $endOfRange);
        }

        $riwayatAbsen = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->orderBy('tanggal', 'desc')
            ->paginate(10)
            ->withQueryString(); 

        $totalHadir = Absensi::where('user_id', $user->id)
            ->whereMonth('tanggal', $bulanSelected)
            ->whereYear('tanggal', $tahunSelected)
            ->whereIn('status', ['Hadir', 'Terlambat'])
            ->count();

        return view('guru.riwayat.index', compact('riwayatAbsen', 'bulanSelected', 'tahunSelected', 'totalHadir'));
    }
}