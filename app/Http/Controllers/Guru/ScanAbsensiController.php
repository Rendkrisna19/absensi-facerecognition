<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\IpLokal;
use App\Models\PengajuanIzin;      
use App\Models\PengaturanAbsensi;
use App\Models\LiburSemester; 
use Carbon\Carbon;
use Illuminate\Support\Str; // <-- WAJIB DITAMBAHKAN UNTUK BACA POLA WIFI (SUBNET)

class ScanAbsensiController extends Controller
{
    /**
     * FUNGSI BANTUAN: Mengecek apakah IP HP Guru termasuk dalam WiFi yang didaftarkan Admin
     */
    private function cekJaringanWifi($ipUser)
    {
        // Ambil semua IP yang berstatus Aktif di database
        $allowedIps = IpLokal::where('is_active', true)->pluck('ip_address');

        foreach ($allowedIps as $allowedIp) {
            // Ubah format '%' dari Admin menjadi '*' agar bisa dibaca fungsi Str::is Laravel
            $pattern = str_replace('%', '*', $allowedIp);
            
            // Cek apakah IP HP cocok dengan pola WiFi (Misal: 192.168.1.* cocok dengan 192.168.1.45)
            if (Str::is($pattern, $ipUser)) {
                return true; // Valid, Guru konek ke WiFi sekolah
            }
        }
        
        return false; // Tidak Valid, Guru pakai paket data / WiFi luar
    }

    public function index()
    {
        $user = auth()->user();
        
        $wajahTerdaftar = !empty($user->face_descriptor);
        
        // Cek Jaringan WIFI Guru Saat Ini
        $ipUser = request()->ip();
        $ipValid = $this->cekJaringanWifi($ipUser);

        $pengaturan = PengaturanAbsensi::first();
        $jamSekarang = Carbon::now()->format('H:i:s');
        $hariIni = Carbon::now()->format('Y-m-d');
        
        $isWaktuAbsen = true;
        $pesanWaktu = '';

        // ---> VALIDASI 1: Cek WiFi Sekolah <---
        if (!$ipValid) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Anda tidak terhubung ke jaringan WiFi sekolah. Silakan hubungkan perangkat Anda ke WiFi sekolah terlebih dahulu.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        // ---> VALIDASI 2: Cek Libur Semester <---
        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->first();

        if ($liburSemester) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Saat ini sedang masa ' . $liburSemester->nama_semester . '. Sistem absensi ditutup.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        // ---> VALIDASI 3: CEK PENGAJUAN IZIN HARI INI <---
        $izinHariIni = PengajuanIzin::where('user_id', $user->id)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->whereIn('status', ['Pending', 'Disetujui'])
                            ->first();

        if ($izinHariIni) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Anda memiliki pengajuan ' . $izinHariIni->jenis . ' (' . $izinHariIni->status . ') hari ini. Anda tidak dapat melakukan absensi.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        // ---> VALIDASI 4: CEK WAKTU <---
        if (!$pengaturan) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Pengaturan jadwal absensi belum dikonfigurasi.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $jamTutupAbsen = $pengaturan->jam_tutup_absen ?? '23:59:59';

        if ($jamSekarang < $jamBukaAbsen) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Absensi baru bisa dimulai pukul ' . Carbon::parse($jamBukaAbsen)->format('H:i') . ' WIB.';
        } elseif ($jamSekarang > $jamTutupAbsen) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Batas waktu absensi masuk telah habis (Tutup ' . Carbon::parse($jamTutupAbsen)->format('H:i') . ' WIB).';
        }

        return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $hariIni = Carbon::now()->format('Y-m-d');
        $jamSekarang = Carbon::now()->format('H:i:s');
        
        // ---> VALIDASI API 1: Cek WIFI Sekolah <---
        $ipUser = request()->ip();
        if (!$this->cekJaringanWifi($ipUser)) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Anda tidak terhubung ke jaringan WiFi sekolah. (IP Perangkat Anda: '.$ipUser.')'
            ]);
        }

        // ---> VALIDASI API 2: Cek Libur Semester <---
        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->first();

        if ($liburSemester) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Saat ini sedang masa ' . $liburSemester->nama_semester . '. Sistem ditutup.'
            ]);
        }

        // ---> VALIDASI API 3: Cek Pengajuan Izin Hari Ini <---
        $izinHariIni = PengajuanIzin::where('user_id', $user->id)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->whereIn('status', ['Pending', 'Disetujui'])
                            ->first();

        if ($izinHariIni) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Anda memiliki pengajuan ' . $izinHariIni->jenis . ' (' . $izinHariIni->status . ') untuk hari ini.'
            ]);
        }

        // ---> VALIDASI API 4: Jadwal Buka / Tutup Absen <---
        $pengaturan = PengaturanAbsensi::first();

        if (!$pengaturan) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Pengaturan jadwal absensi belum dikonfigurasi.'
            ]);
        }

        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $jamTutupAbsen = $pengaturan->jam_tutup_absen ?? '23:59:59';
        $batasMasuk    = $pengaturan->batas_jam_masuk ?? '07:15:00';
        
        if ($jamSekarang < $jamBukaAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Absensi belum dibuka. Silakan kembali pada pukul ' . Carbon::parse($jamBukaAbsen)->format('H:i') . ' WIB.'
            ]);
        }

        if ($jamSekarang > $jamTutupAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Batas waktu absensi untuk hari ini sudah ditutup.'
            ]);
        }
        
        // ---> VALIDASI API 5: Cek Sudah Absen / Belum <---
        $sudahAbsen = Absensi::where('user_id', $user->id)
                             ->where('tanggal', $hariIni)
                             ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.'
            ]);
        }

        // ---> SIMPAN KE DATABASE <---
        $status = 'Hadir';
        $menitTerlambat = 0;

        if ($jamSekarang > $batasMasuk) {
            $status = 'Terlambat';
            $waktuBatas = Carbon::parse($batasMasuk);
            $waktuMasuk = Carbon::parse($jamSekarang);
            $menitTerlambat = $waktuBatas->diffInMinutes($waktuMasuk);
        }

        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $hariIni,
            'jam_masuk' => $jamSekarang,
            'status' => $status,
            'menit_terlambat' => $menitTerlambat
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat sebagai ' . $status . ' pada ' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB.'
        ]);
    }
}