<?php

namespace App\Http\Controllers\KepalaYayasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanAbsensiExport;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil data guru untuk dropdown
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        // 2. Set default tanggal (Awal bulan sampai Akhir bulan saat ini) jika filter kosong
        $startDate = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? Carbon::now()->endOfMonth()->toDateString();

        // 3. Query menggunakan Eloquent Best Practice (when)
        $absensis = Absensi::with(['user.guru'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->when($request->filled('guru_id'), function ($query) use ($request) {
                return $query->where('user_id', $request->guru_id);
            })
            ->orderBy('tanggal', 'desc')
            ->get(); // Gunakan get() agar pagination di-handle oleh Javascript (Tanpa Reload)

        // 4. Hitung Rekap Data untuk Card Summary berdasarkan hasil filter
        $summary = [
            'hadir' => $absensis->where('status', 'Hadir')->count(),
            'terlambat' => $absensis->where('status', 'Terlambat')->count(),
            'alpa' => $absensis->where('status', 'Alpa')->count(),
            'total' => $absensis->count(),
        ];

        return view('kepala-yayasan.laporan.index', compact('absensis', 'gurus', 'summary', 'startDate', 'endDate'));
    }

    public function exportPdf(Request $request)
    {
        Carbon::setLocale('id');
        
        // 1. Tangkap parameter filter yang sama dengan halaman index
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $guruId = $request->input('guru_id');

        // 2. Query data sesuai filter
        $query = Absensi::with('user')->whereBetween('tanggal', [$startDate, $endDate]);
        if ($guruId) {
            $query->where('user_id', $guruId);
        }
        
        $absensis = $query->orderBy('tanggal', 'asc')->get();

        // 3. Render ke PDF
        $pdf = Pdf::loadView('kepala-yayasan.laporan.pdf', compact('absensis', 'startDate', 'endDate'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream('Laporan_Kehadiran_'.$startDate.'_sd_'.$endDate.'.pdf');
    }

    // Method Export Excel
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $guruId = $request->input('guru_id');

        return Excel::download(new LaporanAbsensiExport($startDate, $endDate, $guruId), 'Laporan_Kehadiran_'.$startDate.'_sd_'.$endDate.'.xlsx');
    }
}