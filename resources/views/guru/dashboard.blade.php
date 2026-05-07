@extends('layouts.mobile')

@section('title', 'Beranda')
@section('subtitle', 'Halo, Selamat Pagi')
@section('page_title', auth()->user()->name)

@section('content')
<div class="space-y-6">

    <!-- Header Tanggal Hari Ini -->
    <div class="flex items-center justify-between px-1">
        <div>
            <h2 class="text-gray-800 font-bold text-lg">Hari Ini</h2>
            <p class="text-gray-500 text-sm flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-[#002D8B]"></i> 
                {{ $tanggalFormat }}
            </p>
        </div>
        <!-- Real-time Clock -->
        <div class="bg-white border border-gray-200 px-3 py-1.5 rounded-xl text-sm font-bold text-[#002D8B] shadow-sm flex items-center gap-2" id="realtime-clock">
            <i class="fa-regular fa-clock"></i> <span>--:--</span>
        </div>
    </div>

    <!-- KARTU UTAMA (Berubah sesuai status: Libur / Sudah Absen / Belum Absen) -->
    
    @if($isLibur)
        <!-- MODE HARI LIBUR -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden border border-teal-400">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 text-center py-4">
                <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                    <i class="fa-solid fa-mug-hot text-3xl"></i>
                </div>
                <h2 class="text-2xl font-bold mb-1">Hari Libur</h2>
                <p class="text-teal-100 text-sm font-medium bg-black/10 inline-block px-3 py-1 rounded-full mt-2">
                    {{ $keteranganLibur }}
                </p>
                <p class="text-xs text-teal-100 mt-4">Selamat beristirahat, tidak perlu melakukan absensi hari ini.</p>
            </div>
        </div>

    @else
        <!-- MODE HARI KERJA -->
        <div class="bg-[#002D8B] rounded-3xl p-6 text-white shadow-lg relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="mb-6">
                    <p class="text-sm text-blue-200 font-medium mb-1">Status Kehadiran Anda</p>
                    <h2 class="text-3xl font-bold flex items-center gap-2">
                        @if($absenHariIni)
                            <i class="fa-solid fa-circle-check text-green-400"></i> Selesai
                        @else
                            <i class="fa-solid fa-circle-exclamation text-orange-400"></i> Belum Absen
                        @endif
                    </h2>
                </div>

                @if($absenHariIni)
                    <!-- Jika Sudah Absen -->
                    <div class="bg-white/10 rounded-xl p-4 flex items-center justify-between backdrop-blur-md border border-white/10">
                        <div>
                            <p class="text-xs text-blue-200 mb-0.5">Jam Masuk</p>
                            <p class="font-bold text-lg font-mono">{{ \Carbon\Carbon::parse($absenHariIni->jam_masuk)->format('H:i') }} WIB</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-blue-200 mb-0.5">Status</p>
                            @if($absenHariIni->status == 'Terlambat')
                                <span class="bg-orange-500 text-white text-xs font-bold px-2 py-1 rounded">Terlambat</span>
                            @else
                                <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded">Hadir Tepat Waktu</span>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Jika Belum Absen -->
                    <a href="{{ route('guru.scan') }}" class="flex items-center justify-center gap-3 w-full bg-orange-400 hover:bg-orange-500 text-white font-bold py-3.5 rounded-xl transition-all shadow-md active:scale-95">
                        <i class="fa-solid fa-camera text-xl"></i> Buka Kamera Absensi
                    </a>
                    <p class="text-center text-xs text-blue-200 mt-3"><i class="fa-solid fa-circle-info mr-1"></i> Pastikan Anda terhubung dengan WiFi sekolah.</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Informasi Ringkas Bulanan (DATA REAL) -->
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col justify-between h-28 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-green-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mb-2 relative z-10">
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] text-gray-500 font-semibold mb-0.5 uppercase tracking-wide">Hadir Bulan Ini</p>
                <h4 class="text-2xl font-bold text-gray-800">{{ $totalHadirBulanIni }} <span class="text-sm font-normal text-gray-400">Hari</span></h4>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col justify-between h-28 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-50 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <!-- Hiasan background rupiah -->
            <i class="fa-solid fa-rupiah-sign absolute -right-2 -bottom-2 text-5xl text-red-50 opacity-50"></i>
            <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-2 relative z-10">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div class="relative z-10">
                <p class="text-[11px] text-gray-500 font-semibold mb-0.5 uppercase tracking-wide">Potongan Gaji</p>
                <h4 class="text-lg font-bold text-red-600">Rp {{ number_format($totalDendaBulanIni, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    // Script Jam Realtime dengan format yang lebih rapi
    function updateClock() {
        const date = new Date();
        const hours = date.getHours().toString().padStart(2, '0');
        const minutes = date.getMinutes().toString().padStart(2, '0');
        
        // Animasi titik dua berkedip setiap detik
        const separator = date.getSeconds() % 2 === 0 ? ':' : '<span class="opacity-50">:</span>';
        
        document.getElementById('realtime-clock').innerHTML = 
            `<i class="fa-regular fa-clock"></i> <span>${hours}${separator}${minutes}</span>`;
    }
    
    setInterval(updateClock, 1000);
    updateClock(); // Panggil sekali di awal agar tidak delay 1 detik
</script>
@endpush
@endsection