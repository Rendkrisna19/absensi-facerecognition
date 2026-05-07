    <?php

    namespace App\Http\Controllers\Guru;

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use App\Models\Absensi;
    use App\Models\IpLokal;
    use App\Models\PengaturanAbsensi;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Storage;

    class AbsensiGuruController extends Controller
    {
        // Menampilkan Halaman Beranda (Dashboard)
    public function dashboard()
        {
            $user = auth()->user();
            
            // Atur bahasa waktu ke Indonesia
            Carbon::setLocale('id');
            $hariIni = Carbon::now();
            $bulanIni = $hariIni->month;
            $tahunIni = $hariIni->year;
            $tanggalFormat = $hariIni->translatedFormat('l, d F Y');
            
            // 1. Cek apakah sudah absen hari ini
            $absenHariIni = Absensi::where('user_id', $user->id)
                                ->where('tanggal', $hariIni->format('Y-m-d'))
                                ->first();

            // 2. LOGIKA HARI LIBUR
            $isLibur = false;
            $keteranganLibur = '';

            if ($hariIni->isSunday()) {
                $isLibur = true;
                $keteranganLibur = 'Libur Akhir Pekan (Minggu)';
            } else {
                try {
                    $response = Http::timeout(3)->get('https://dayoffapi.vercel.app/api?month=' . $hariIni->month . '&year=' . $hariIni->year);
                    if ($response->successful()) {
                        $liburNasional = $response->json();
                        foreach ($liburNasional as $libur) {
                            if ($libur['tanggal'] === $hariIni->format('Y-m-d') && $libur['is_cuti'] === false) {
                                $isLibur = true;
                                $keteranganLibur = $libur['keterangan'];
                                break;
                            }
                        }
                    }
                } catch (\Exception $e) {
                    // Abaikan jika API mati
                }
            }

            // 3. AMBIL DATA REAL UNTUK KARTU INFORMASI (Bulan Ini)
            
            // Menghitung total hari hadir di bulan ini (semua status kecuali Alpa)
            $totalHadirBulanIni = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', '!=', 'Alpa')
                ->count();

            // Mengambil nominal denda flat dari pengaturan
            $pengaturan = PengaturanAbsensi::first();
            $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

            // Menghitung hari terlambat bulan ini
            $totalTelatBulanIni = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $bulanIni)
                ->whereYear('tanggal', $tahunIni)
                ->where('status', 'Terlambat')
                ->count();

            // Total estimasi potongan denda
            $totalDendaBulanIni = $totalTelatBulanIni * $nominalDendaFlat;

            // Kirim semua variabel ke View
            return view('guru.dashboard', compact(
                'absenHariIni', 
                'tanggalFormat', 
                'isLibur', 
                'keteranganLibur',
                'totalHadirBulanIni',
                'totalDendaBulanIni'
            ));
        }

        // Menampilkan Halaman Kamera & Validasi IP
       // Menampilkan Halaman Kamera & Validasi IP
    public function scan()
    {
        $user = auth()->user();
        
        // 1. Validasi: Wajah Sudah Terdaftar?
        $wajahTerdaftar = !empty($user->face_descriptor);

        // 2. Validasi: Cek IP Jaringan
        $ipUser = request()->ip();
        $ipValid = IpLokal::where('ip_address', $ipUser)->where('is_active', true)->exists();

        // 3. Validasi: CEK WAKTU (Jam Buka & Tutup Absen)
        $pengaturan = PengaturanAbsensi::first();
        $jamSekarang = Carbon::now()->format('H:i:s');
        
        // Gunakan default jam 06:00 - 12:00 jika kolom di database belum dibuat
        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '06:00:00'; 
        $jamTutupAbsen = $pengaturan->jam_tutup_absen ?? '12:00:00';

        $isWaktuAbsen = true;
        $pesanWaktu = '';

        if ($jamSekarang < $jamBukaAbsen) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Absensi belum dibuka. Anda baru bisa absen mulai pukul ' . Carbon::parse($jamBukaAbsen)->format('H:i') . ' WIB.';
        } elseif ($jamSekarang > $jamTutupAbsen) {
            $isWaktuAbsen = false;
            $pesanWaktu = 'Batas waktu absensi masuk telah habis. Tutup pukul ' . Carbon::parse($jamTutupAbsen)->format('H:i') . ' WIB.';
        }

        return view('guru.scan', compact('wajahTerdaftar', 'ipValid', 'ipUser', 'pengaturan', 'isWaktuAbsen', 'pesanWaktu'));
    }

    // Menyimpan Absen
    public function storeAbsensi(Request $request)
    {
        $user = auth()->user();
        $hariIni = Carbon::now()->format('Y-m-d');
        $jamSekarang = Carbon::now()->format('H:i:s');
        
        $pengaturan = PengaturanAbsensi::first();
        $batasMasuk = $pengaturan ? $pengaturan->batas_jam_masuk : '07:15:00';
        $jamBukaAbsen = $pengaturan->jam_buka_absen ?? '06:00:00';
        $jamTutupAbsen = $pengaturan->jam_tutup_absen ?? '12:00:00';

        // Validasi Lapis 2: Tolak dari backend jika ditembak di luar jam operasional
        if ($jamSekarang < $jamBukaAbsen || $jamSekarang > $jamTutupAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal! Saat ini di luar jam operasional absensi.'
            ]);
        }
        
        // Cek apakah hari ini sudah absen
        $sudahAbsen = Absensi::where('user_id', $user->id)
                             ->where('tanggal', $hariIni)
                             ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absensi hari ini.'
            ]);
        }

        // Tentukan Status (Hadir / Terlambat)
        $status = 'Hadir';
        $menitTerlambat = 0;

        if ($jamSekarang > $batasMasuk) {
            $status = 'Terlambat';
            // Hitung selisih menit keterlambatan
            $waktuBatas = Carbon::parse($batasMasuk);
            $waktuMasuk = Carbon::parse($jamSekarang);
            $menitTerlambat = $waktuBatas->diffInMinutes($waktuMasuk);
        }

        // Simpan ke database
        Absensi::create([
            'user_id' => $user->id,
            'tanggal' => $hariIni,
            'jam_masuk' => $jamSekarang,
            'jam_pulang' => null, // Dikosongkan dulu untuk fitur pulang nanti
            'status' => $status,
            'menit_terlambat' => $menitTerlambat
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil dicatat pada ' . $jamSekarang . ' WIB.'
        ]);
    }


        //riwayat absensi
        // Jangan lupa pastikan 'use Illuminate\Http\Request;' sudah ada di paling atas file
        
        public function riwayat(Request $request)
        {
            $user = auth()->user();
            Carbon::setLocale('id');

            // 1. Ambil input filter dari user, jika kosong gunakan bulan & tahun saat ini
            $bulanSelected = $request->input('bulan', Carbon::now()->month);
            $tahunSelected = $request->input('tahun', Carbon::now()->year);

            // 2. Query dengan Filter dan Pagination (10 data per halaman)
            $riwayatAbsen = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $bulanSelected)
                ->whereYear('tanggal', $tahunSelected)
                ->orderBy('tanggal', 'desc')
                ->paginate(10)
                ->withQueryString(); // withQueryString agar filter tidak hilang saat pindah halaman (Next/Prev)

            // 3. Menghitung total kehadiran khusus untuk bulan yang difilter
            $totalHadir = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $bulanSelected)
                ->whereYear('tanggal', $tahunSelected)
                ->where('status', '!=', 'Alpa')
                ->count();

            return view('guru.riwayat', compact('riwayatAbsen', 'bulanSelected', 'tahunSelected', 'totalHadir'));
        }

        public function denda(Request $request)
        {
            $user = auth()->user();
            Carbon::setLocale('id');

            // 1. Ambil input filter, default ke bulan dan tahun sekarang
            $bulanSelected = $request->input('bulan', Carbon::now()->month);
            $tahunSelected = $request->input('tahun', Carbon::now()->year);

            // Ambil pengaturan untuk tahu nominal denda flat
            $pengaturan = PengaturanAbsensi::first();
            $nominalDendaFlat = $pengaturan ? $pengaturan->denda_terlambat : 0;

            // Ambil riwayat khusus yang statusnya "Terlambat" berdasarkan filter bulan & tahun
            $riwayatTerlambat = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', $bulanSelected)
                ->whereYear('tanggal', $tahunSelected)
                ->where('status', 'Terlambat')
                ->orderBy('tanggal', 'desc')
                ->get();

            $totalHariTelat = $riwayatTerlambat->count();
            $totalDenda = $totalHariTelat * $nominalDendaFlat;

            // Buat nama bulan & tahun untuk ditampilkan di kartu denda (Contoh: "Mei 2026")
            $namaBulanTahun = Carbon::createFromDate($tahunSelected, $bulanSelected, 1)->translatedFormat('F Y');

            return view('guru.denda', compact(
                'riwayatTerlambat', 
                'totalHariTelat', 
                'totalDenda', 
                'nominalDendaFlat',
                'bulanSelected',
                'tahunSelected',
                'namaBulanTahun'
            ));
        }

        public function pengaturan()
        {
            $user = auth()->user();
            return view('guru.pengaturan', compact('user'));
        }

        public function updateProfil(Request $request)
        {
            $request->validate([
                'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Maksimal 2MB
            ]);

            $user = auth()->user();

            if ($request->hasFile('foto_profil')) {
                // Hapus foto lama jika ada
                if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                    Storage::delete('public/' . $user->foto_profil);
                }

                // Simpan foto baru ke folder storage/app/public/profil
                $path = $request->file('foto_profil')->store('profil', 'public');
                
                // Update nama file di database
                $user->update(['foto_profil' => $path]);
            }

            return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
        }

        public function storeAbsensi(Request $request)
        {
            $user = auth()->user();
            $hariIni = Carbon::now()->format('Y-m-d');
            $jamSekarang = Carbon::now()->format('H:i:s');
            
            // Ambil pengaturan absensi untuk jam batas telat
            $pengaturan = PengaturanAbsensi::first();
            $batasMasuk = $pengaturan ? $pengaturan->batas_jam_masuk : '07:15:00';
            
            // Cek apakah hari ini sudah absen
            $sudahAbsen = Absensi::where('user_id', $user->id)
                                ->where('tanggal', $hariIni)
                                ->exists();

            if ($sudahAbsen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan absensi hari ini.'
                ]);
            }

            // Tentukan Status (Hadir / Terlambat)
            $status = 'Hadir';
            $menitTerlambat = 0;

            if ($jamSekarang > $batasMasuk) {
                $status = 'Terlambat';
                // Hitung selisih menit keterlambatan
                $waktuBatas = Carbon::parse($batasMasuk);
                $waktuMasuk = Carbon::parse($jamSekarang);
                $menitTerlambat = $waktuBatas->diffInMinutes($waktuMasuk);
            }

            // Simpan ke database
            Absensi::create([
                'user_id' => $user->id,
                'tanggal' => $hariIni,
                'jam_masuk' => $jamSekarang,
                'jam_pulang' => null, // Dikosongkan dulu untuk fitur pulang nanti
                'status' => $status,
                'menit_terlambat' => $menitTerlambat
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil dicatat pada ' . $jamSekarang . ' WIB.'
            ]);
        }
    }
