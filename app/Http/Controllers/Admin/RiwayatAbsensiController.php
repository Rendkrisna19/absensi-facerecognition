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

    public function cleanup(Request $request)
    {
        $request->validate([
            'tipe_hapus' => 'required|in:hari,minggu,semua',
            'tanggal' => 'required_if:tipe_hapus,hari|date',
            'minggu' => 'required_if:tipe_hapus,minggu', 
        ]);

        try {
            $query = Absensi::query();
            $pesan = '';

            if ($request->tipe_hapus == 'hari') {
                $query->where('tanggal', $request->tanggal);
                $pesan = 'Data absensi tanggal ' . \Carbon\Carbon::parse($request->tanggal)->format('d-m-Y') . ' berhasil dihapus.';
            } elseif ($request->tipe_hapus == 'minggu') {
                // $request->minggu is typically '2026-W01'
                $parts = explode('-W', $request->minggu);
                if(count($parts) == 2) {
                    $year = $parts[0];
                    $week = $parts[1];
                    $startOfWeek = Carbon::now()->setISODate($year, $week)->startOfWeek();
                    $endOfWeek = $startOfWeek->copy()->endOfWeek();
                    $query->whereBetween('tanggal', [$startOfWeek->format('Y-m-d'), $endOfWeek->format('Y-m-d')]);
                    $pesan = 'Data absensi minggu ke-' . $week . ' tahun ' . $year . ' berhasil dihapus.';
                } else {
                    return back()->with('error', 'Format minggu tidak valid.');
                }
            } elseif ($request->tipe_hapus == 'semua') {
                $pesan = 'Semua data absensi berhasil dibersihkan.';
            }

            $deleted = $query->delete();
            return back()->with('success', $pesan . ' (' . $deleted . ' baris terhapus)');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membersihkan data: ' . $e->getMessage());
        }
    }
}