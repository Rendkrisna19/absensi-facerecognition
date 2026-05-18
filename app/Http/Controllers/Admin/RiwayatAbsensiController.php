<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar mengambil data absensi beserta relasi user
        $query = Absensi::with('user')->orderBy('tanggal', 'desc')->orderBy('jam_masuk', 'desc');

        // Filter 1: Pencarian Nama atau NIK
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter 2: Status (Hadir, Terlambat, Alpa, Sakit, Izin, Cuti)
        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter 3: Bulan
        if ($request->filled('bulan') && $request->bulan != 'all') {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter 4: Tahun
        if ($request->filled('tahun') && $request->tahun != 'all') {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Eksekusi Query dengan Pagination (15 data per halaman)
        $riwayat = $query->paginate(15)->withQueryString();

        return view('admin.riwayat_absensi.index', compact('riwayat'));
    }
}