@extends('layouts.mobile') 
@section('title', 'Beranda')
@section('subtitle', 'Halo, Selamat Datang')
@section('page_title', auth()->user()->name)

@section('content')
<div class="px-1 py-2">
    <!-- Header Area -->
    <div class="flex justify-between items-center mb-6 px-1">
        <div>
            <h1 class="text-2xl font-extrabold text-[#1a233a] tracking-tight">Menu Utama</h1>
            <p class="text-gray-500 text-[11px] mt-1 font-medium">{{ $tanggalFormat }}</p>
        </div>
        <div class="bg-white border border-gray-100 px-3 py-1.5 rounded-xl text-xs font-bold text-[#F08600] shadow-sm flex items-center gap-2" id="realtime-clock">
            <i class="fa-regular fa-clock"></i> <span>--:--</span>
        </div>
    </div>

    <!-- Status Banner -->
    @if($izinHariIni)
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-[24px] p-5 text-white shadow-lg relative overflow-hidden border border-amber-400 mb-6">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shrink-0">
                    <i class="fa-solid fa-envelope-open-text text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold mb-0.5">Izin: {{ $izinHariIni->jenis }}</h2>
                    <p class="text-amber-100 text-xs font-medium bg-black/10 inline-block px-2 py-0.5 rounded-md">
                        {{ $izinHariIni->status == 'Pending' ? 'Menunggu Persetujuan' : 'Telah Disetujui' }}
                    </p>
                </div>
            </div>
        </div>

    @elseif($isLibur)
        <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-[24px] p-5 text-white shadow-lg relative overflow-hidden border border-teal-400 mb-6">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm shrink-0">
                    <i class="fa-solid fa-mug-hot text-2xl"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold mb-0.5">Hari Libur</h2>
                    <p class="text-teal-100 text-xs font-medium line-clamp-1">{{ $keteranganLibur }}</p>
                </div>
            </div>
        </div>

    @else
        <div class="bg-[#F08600] rounded-[24px] p-5 text-white shadow-lg relative overflow-hidden mb-6">
            <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-[11px] text-orange-200 font-medium mb-0.5 uppercase tracking-wider">Status Kehadiran</p>
                        <h2 class="text-lg font-bold flex items-center gap-2">
                            @if(!$absenHariIni)
                                <i class="fa-solid fa-circle-exclamation text-yellow-200"></i> Belum Absen
                            @elseif(empty($absenHariIni->jam_pulang))
                                <i class="fa-solid fa-spinner fa-spin text-yellow-200"></i> Waktu Bekerja
                            @else
                                <i class="fa-solid fa-circle-check text-green-300"></i> Selesai
                            @endif
                        </h2>
                    </div>
                </div>

                @if($absenHariIni)
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-white/10 rounded-xl p-2.5 backdrop-blur-md border border-white/10 flex items-center justify-between">
                            <div>
                                <p class="text-[9px] text-orange-200 uppercase tracking-wider">Jam Masuk</p>
                                <p class="font-bold text-base font-mono">{{ \Carbon\Carbon::parse($absenHariIni->jam_masuk)->format('H:i') }}</p>
                            </div>
                            @if($absenHariIni->status == 'Terlambat')
                                <i class="fa-solid fa-triangle-exclamation text-red-400"></i>
                            @else
                                <i class="fa-solid fa-check text-green-400"></i>
                            @endif
                        </div>
                        
                        <div class="bg-white/10 rounded-xl p-2.5 backdrop-blur-md border border-white/10 flex items-center justify-between">
                            <div>
                                <p class="text-[9px] text-orange-200 uppercase tracking-wider">Jam Pulang</p>
                                @if(!empty($absenHariIni->jam_pulang))
                                    <p class="font-bold text-base font-mono">{{ \Carbon\Carbon::parse($absenHariIni->jam_pulang)->format('H:i') }}</p>
                                @else
                                    <p class="font-bold text-base font-mono text-white/50">--:--</p>
                                @endif
                            </div>
                            @if(!empty($absenHariIni->jam_pulang))
                                <i class="fa-solid fa-check text-green-400"></i>
                            @else
                                <i class="fa-regular fa-clock text-white/50"></i>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-xs text-orange-100"><i class="fa-solid fa-circle-info mr-1"></i> Silakan tekan tombol Absensi di bawah.</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Grid Menu -->
    <div class="grid grid-cols-2 gap-4">
        
        <!-- Scan Absensi -->
        <a href="{{ route('guru.scan') }}" class="bg-white rounded-[24px] p-6 flex flex-col items-center text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all active:scale-95">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-500 flex items-center justify-center text-2xl mb-4">
                <i class="fa-regular fa-user"></i>
            </div>
            <h3 class="font-bold text-[#1a233a] text-[13px] mb-1">Absensi</h3>
            <p class="text-[10px] text-gray-400 font-medium">Scan Masuk / Pulang</p>
        </a>

        <!-- Riwayat -->
        <a href="{{ route('guru.riwayat') }}" class="bg-white rounded-[24px] p-6 flex flex-col items-center text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all active:scale-95">
            <div class="w-14 h-14 rounded-2xl bg-orange-50 text-[#F08600] flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-gift"></i>
            </div>
            <h3 class="font-bold text-[#1a233a] text-[13px] mb-1">Riwayat</h3>
            <p class="text-[10px] text-gray-400 font-medium">{{ $totalHadirBulanIni }} Kehadiran</p>
        </a>

        <!-- Izin -->
        <a href="{{ route('guru.pengajuan-izin.index') }}" class="bg-white rounded-[24px] p-6 flex flex-col items-center text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all active:scale-95">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-lock-open"></i>
            </div>
            <h3 class="font-bold text-[#1a233a] text-[13px] mb-1">Izin & Cuti</h3>
            <p class="text-[10px] text-gray-400 font-medium">Pengajuan</p>
        </a>

        <!-- Denda -->
        <a href="{{ route('guru.denda') }}" class="bg-white rounded-[24px] p-6 flex flex-col items-center text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all active:scale-95">
            <div class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-user-group"></i>
            </div>
            <h3 class="font-bold text-[#1a233a] text-[13px] mb-1">Data Denda</h3>
            <p class="text-[10px] text-gray-400 font-medium">Rp {{ number_format($totalDendaBulanIni, 0, ',', '.') }}</p>
        </a>

        <!-- Pengaturan -->
        <a href="{{ route('guru.pengaturan') }}" class="bg-white rounded-[24px] p-6 flex flex-col items-center text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all active:scale-95">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl mb-4">
                <i class="fa-regular fa-calendar"></i>
            </div>
            <h3 class="font-bold text-[#1a233a] text-[13px] mb-1">Pengaturan</h3>
            <p class="text-[10px] text-gray-400 font-medium">Profil Akun</p>
        </a>

        <!-- Informasi -->
        <div class="bg-white rounded-[24px] p-6 flex flex-col items-center text-center shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-all cursor-help active:scale-95" onclick="Swal.fire('Informasi', 'Selamat datang di Aplikasi Absensi SMA CODIFYHUB.', 'info')">
            <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-500 flex items-center justify-center text-2xl mb-4">
                <i class="fa-solid fa-circle-plus"></i>
            </div>
            <h3 class="font-bold text-[#1a233a] text-[13px] mb-1">Informasi</h3>
            <p class="text-[10px] text-gray-400 font-medium">Bantuan Sistem</p>
        </div>

    </div>
</div>
@endsection