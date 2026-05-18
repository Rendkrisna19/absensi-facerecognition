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
use Illuminate\Support\Str;

class ScanAbsensiController extends Controller
{
    private function cekJaringanWifi($ipUser)
    {
        $allowedIps = IpLokal::where('is_active', true)->pluck('ip_address');
        foreach ($allowedIps as $allowedIp) {
            $pattern = str_replace('%', '*', $allowedIp);
            if (Str::is($pattern, $ipUser)) return true;
        }
        return false;
    }

    public function index()
    {
        $user = auth()->user();
        $wajahTerdaftar = !empty($user->face_descriptor);
        
        $ipUser = request()->ip();
        $ipValid = $this->cekJaringanWifi($ipUser);

        $pengaturan = PengaturanAbsensi::first();
        $jamSekarang = Carbon::now()->format('H:i:s');
        $hariIni = Carbon::now()->format('Y-m-d');
        
        $isWaktuAbsen = true;
        $pesanWaktu = '';

        // ---> VALIDASI JARINGAN & LIBUR & IZIN <---
        if (!$ipValid) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Anda tidak terhubung ke jaringan WiFi sekolah.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        $liburSemester = LiburSemester::where('is_active', true)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->first();
        if ($liburSemester) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Saat ini sedang masa ' . $liburSemester->nama_semester . '. Sistem absensi ditutup.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        $izinHariIni = PengajuanIzin::where('user_id', $user->id)
                            ->whereDate('tanggal_mulai', '<=', $hariIni)
                            ->whereDate('tanggal_selesai', '>=', $hariIni)
                            ->whereIn('status', ['Pending', 'Disetujui'])
                            ->first();
        if ($izinHariIni) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Anda memiliki pengajuan ' . $izinHariIni->jenis . ' hari ini. Anda tidak dapat melakukan absensi.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        if (!$pengaturan) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Pengaturan jadwal absensi belum dikonfigurasi.';
            return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
        }

        // ---> LOGIKA STATUS ABSENSI (MASUK / PULANG) <---
        $absenHariIni = Absensi::where('user_id', $user->id)->where('tanggal', $hariIni)->first();
        
        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $jamPulang    = $pengaturan->jam_pulang ?? '14:00:00';

        if (!$absenHariIni) {
            // BELUM ABSEN MASUK
            if ($jamSekarang < $jamBukaAbsen) {
                $isWaktuAbsen = false;
                $pesanWaktu = 'Absensi MASUK belum dibuka. Silakan kembali pada pukul ' . Carbon::parse($jamBukaAbsen)->format('H:i') . ' WIB.';
            } elseif ($jamSekarang >= $jamPulang) {
                $isWaktuAbsen = false;
                $pesanWaktu = 'Batas waktu absensi MASUK hari ini sudah ditutup karena sudah masuk jam pulang.';
            }
        } else {
            // SUDAH ABSEN MASUK
            if (empty($absenHariIni->jam_pulang)) {
                // BELUM ABSEN PULANG
                if ($jamSekarang < $jamPulang) {
                    $isWaktuAbsen = false;
                    $pesanWaktu = 'Anda sudah Absen Masuk. Absensi PULANG baru akan dibuka pukul ' . Carbon::parse($jamPulang)->format('H:i') . ' WIB.';
                }
            } else {
                // SUDAH ABSEN PULANG
                $isWaktuAbsen = false;
                $pesanWaktu = 'Anda telah menyelesaikan Absensi Masuk dan Pulang hari ini. Selamat beristirahat!';
            }
        }

        return view('guru.scan.index', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $hariIni = Carbon::now()->format('Y-m-d');
        $jamSekarang = Carbon::now()->format('H:i:s');
        
        $ipUser = request()->ip();
        if (!$this->cekJaringanWifi($ipUser)) {
            return response()->json(['success' => false, 'message' => 'Gagal! Anda tidak terhubung ke WiFi sekolah.']);
        }

        $pengaturan = PengaturanAbsensi::first();
        if (!$pengaturan) {
            return response()->json(['success' => false, 'message' => 'Gagal! Pengaturan jadwal absensi belum ada.']);
        }

        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '00:00:00'; 
        $batasMasuk   = $pengaturan->batas_jam_masuk ?? '07:15:00';
        $jamPulang    = $pengaturan->jam_pulang ?? '14:00:00';
        
        $absenHariIni = Absensi::where('user_id', $user->id)->where('tanggal', $hariIni)->first();

        // ========================================================
        // EKSEKUSI PENYIMPANAN DATA (MASUK ATAU PULANG)
        // ========================================================
        if (!$absenHariIni) {
            
            // 1. PROSES ABSEN MASUK
            if ($jamSekarang < $jamBukaAbsen) {
                return response()->json(['success' => false, 'message' => 'Absensi masuk belum dibuka.']);
            }
            if ($jamSekarang >= $jamPulang) {
                return response()->json(['success' => false, 'message' => 'Batas waktu absen masuk sudah ditutup.']);
            }

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
                'message' => 'Absensi MASUK berhasil dicatat pada ' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB.'
            ]);

        } else {
            
            // 2. PROSES ABSEN PULANG
            if (!empty($absenHariIni->jam_pulang)) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan Absen Pulang hari ini.']);
            }

            if ($jamSekarang < $jamPulang) {
                return response()->json(['success' => false, 'message' => 'Belum waktunya pulang! Tunggu hingga pukul ' . Carbon::parse($jamPulang)->format('H:i')]);
            }

            try {
                // MENGGUNAKAN METODE SAVE() UNTUK MEMAKSA UPDATE DATABASE
                $absenHariIni->jam_pulang = $jamSekarang;
                $absenHariIni->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Absensi PULANG berhasil dicatat. Hati-hati di jalan! (' . Carbon::parse($jamSekarang)->format('H:i') . ' WIB)'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mencatat! Pastikan Anda sudah menjalankan migration untuk menambah kolom "jam_pulang" di tabel "absensis".'
                ]);
            }
        }
    }
}