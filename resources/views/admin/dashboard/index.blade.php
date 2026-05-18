@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Ringkasan Hari Ini')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="space-y-8 font-poppins pb-8">
    
    <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 opacity-50 blur-3xl"></div>
        
        <div class="relative z-10">
            <h3 class="text-3xl font-bold text-gray-800">Selamat Datang, <span class="text-[#1e3b8b]">{{ auth()->user()->name ?? 'Admin' }}</span>! 👋</h3>
            <p class="text-gray-500 mt-2 text-sm md:text-base">Pantau presensi dan kedisiplinan guru hari ini secara real-time dengan mudah.</p>
        </div>
        <div class="w-full md:w-auto text-left md:text-right relative z-10">
            <div class="inline-flex items-center bg-white px-5 py-3 rounded-2xl font-bold border border-gray-200 shadow-sm transition-transform hover:scale-105">
                <div class="bg-blue-100 p-2 rounded-xl mr-3">
                    <i class="fa-regular fa-calendar-check text-xl text-[#3b82f6]"></i> 
                </div>
                <span class="text-[#1e3b8b]">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-blue-600 hover:to-blue-800 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-blue-50 text-blue-600 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:rotate-6">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-blue-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Total Guru</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['total_guru'] }} <span class="text-base font-medium text-gray-400 group-hover:text-blue-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-emerald-500 hover:to-emerald-700 hover:shadow-xl hover:shadow-emerald-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-green-50 text-emerald-500 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:rotate-6">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-emerald-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Hadir Tepat</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['hadir'] }} <span class="text-base font-medium text-gray-400 group-hover:text-emerald-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-amber-500 hover:to-orange-600 hover:shadow-xl hover:shadow-orange-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-orange-50 text-orange-500 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:-rotate-6">
                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-orange-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Terlambat</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['terlambat'] }} <span class="text-base font-medium text-gray-400 group-hover:text-orange-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:bg-gradient-to-br hover:from-rose-500 hover:to-rose-700 hover:shadow-xl hover:shadow-rose-500/30 hover:-translate-y-2 transition-all duration-300 ease-in-out cursor-pointer">
            <div class="absolute right-0 top-0 mt-6 mr-6 bg-red-50 text-rose-500 p-4 rounded-2xl transition-all duration-300 group-hover:bg-white/20 group-hover:text-white group-hover:scale-110 group-hover:rotate-6">
                <i class="fa-solid fa-circle-xmark text-2xl"></i>
            </div>
            <p class="text-gray-500 group-hover:text-rose-100 text-sm font-semibold uppercase tracking-wider transition-colors duration-300 mt-2">Belum Absen</p>
            <h4 class="text-4xl font-bold text-gray-800 group-hover:text-white mt-3 transition-colors duration-300">
                {{ $stats['alpa'] }} <span class="text-base font-medium text-gray-400 group-hover:text-rose-200 normal-case tracking-normal transition-colors duration-300">orang</span>
            </h4>
        </div>

    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-gray-100 pb-5 gap-4">
            <div>
                <h4 class="text-xl font-bold text-gray-800">Aktivitas Masuk Terbaru</h4>
                <p class="text-sm text-gray-500 mt-1">Daftar absensi terakhir hari ini.</p>
            </div>
            <a href="#" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold text-[#1e3b8b] bg-blue-50 rounded-xl hover:bg-[#1e3b8b] hover:text-white transition-colors duration-300">
                Lihat Semua Data <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto rounded-2xl border border-gray-100">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 text-gray-600 border-b border-gray-100">
                        <th class="p-5 font-semibold w-16 text-center">No</th>
                        <th class="p-5 font-semibold">Profil Guru</th>
                        <th class="p-5 font-semibold">Waktu Masuk</th>
                        <th class="p-5 font-semibold">Status Presensi</th>
                        <th class="p-5 font-semibold text-center">Metode / Jaringan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent_absensi as $index => $absen)
                    <tr class="hover:bg-blue-50/40 transition-colors duration-200 group">
                        <td class="p-5 text-gray-500 font-medium text-center">{{ $index + 1 }}</td>
                        <td class="p-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 text-blue-700 flex items-center justify-center font-bold text-sm shadow-inner group-hover:scale-105 transition-transform">
                                    {{ strtoupper(substr($absen->user->name ?? 'G', 0, 2)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800 text-base">{{ $absen->user->name ?? 'Guru Tidak Diketahui' }}</span>
                                    <p class="text-xs text-gray-400 font-mono mt-0.5 tracking-wide">NIK: {{ $absen->user->nik ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-5">
                            <span class="inline-flex items-center font-bold text-gray-700 bg-gray-100 px-4 py-2 rounded-xl text-xs">
                                <i class="fa-regular fa-clock text-gray-400 mr-2 text-sm"></i> 
                                {{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }} WIB
                            </span>
                        </td>
                        <td class="p-5">
                            @if($absen->status == 'tepat_waktu')
                                <span class="inline-flex items-center px-4 py-2 bg-emerald-50 text-emerald-600 border border-emerald-100 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-check-circle mr-1.5"></i> Tepat Waktu
                                </span>
                            @elseif($absen->status == 'terlambat')
                                <span class="inline-flex items-center px-4 py-2 bg-orange-50 text-orange-600 border border-orange-100 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Terlambat
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-bold uppercase tracking-wider">
                                    <i class="fa-solid fa-circle-info mr-1.5"></i> {{ $absen->status }}
                                </span>
                            @endif
                        </td>
                        <td class="p-5 text-center">
                            <div class="inline-flex items-center justify-center gap-1.5 text-xs font-bold text-emerald-600 bg-emerald-50/50 px-3 py-1.5 rounded-lg border border-emerald-100/50">
                                <i class="fa-solid fa-wifi"></i> Valid (LAN)
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-16 text-center bg-gray-50/30">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 mb-4">
                                    <i class="fa-regular fa-folder-open text-4xl text-gray-300"></i>
                                </div>
                                <p class="font-bold text-gray-600 text-lg">Belum Ada Presensi Masuk</p>
                                <p class="text-sm mt-1 text-gray-400">Data absensi guru untuk hari ini masih kosong.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection