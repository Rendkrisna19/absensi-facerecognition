<?php

namespace App\Http\Controllers\KepalaYayasan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\User;

class LaporanAbsensiController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua data guru untuk dropdown filter
        $gurus = User::where('role', 'guru')->orderBy('name', 'asc')->get();

        // Query dasar absensi (diurutkan dari yang terbaru)
        $query = Absensi::with(['user.guru'])->orderBy('tanggal', 'desc');

        // Filter berdasarkan Guru
        if ($request->filled('guru_id')) {
            $query->where('user_id', $request->guru_id);
        }

        // Filter berdasarkan Rentang Tanggal
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('start_date')) {
            $query->where('tanggal', '>=', $request->start_date);
        } elseif ($request->filled('end_date')) {
            $query->where('tanggal', '<=', $request->end_date);
        }

        // Eksekusi query (Gunakan paginate agar tidak lemot kalau datanya ribuan)
        $absensis = $query->paginate(20)->withQueryString(); 

        return view('kepala-yayasan.laporan.index', compact('absensis', 'gurus'));
    }
}