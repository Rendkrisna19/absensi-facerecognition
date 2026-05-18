<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminRiwayatAbsensiExport;

class RiwayatAbsensiController extends Controller
{
    private function buildQuery(Request $request)
    {
        $query = Absensi::with('user')->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('bulan') && $request->bulan != 'all') {
            $query->whereMonth('tanggal', $request->bulan);
        }

        if ($request->filled('tahun') && $request->tahun != 'all') {
            $query->whereYear('tanggal', $request->tahun);
        }

        if ($request->filled('unit_sekolah') && $request->unit_sekolah != 'all') {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('unit_sekolah', $request->unit_sekolah);
            });
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->buildQuery($request);
        $riwayat = $query->paginate(15)->withQueryString();

        return view('admin.riwayat_absensi.index', compact('riwayat'));
    }

    public function exportPdf(Request $request)
    {
        Carbon::setLocale('id');
        $query = $this->buildQuery($request);
        $riwayat = $query->get();

        $pdf = Pdf::loadView('admin.riwayat_absensi.pdf', compact('riwayat'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('Riwayat_Absensi_Admin.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(
            new AdminRiwayatAbsensiExport(
                $request->search,
                $request->status,
                $request->bulan,
                $request->tahun,
                $request->unit_sekolah
            ),
            'Riwayat_Absensi_Admin.xlsx'
        );
    }
}