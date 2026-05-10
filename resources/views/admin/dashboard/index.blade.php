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
<div class="space-y-6 font-poppins">
    
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}! 👋</h3>
            <p class="text-gray-500 mt-1">Pantau presensi dan kedisiplinan guru hari ini secara real-time.</p>
        </div>
        <div class="w-full md:w-auto text-left md:text-right">
            <span class="inline-flex items-center text-[#1e3b8b] bg-blue-50 px-4 py-2.5 rounded-xl font-bold border border-blue-100 shadow-sm">
                <i class="fa-regular fa-calendar-check text-lg mr-2 text-[#3b82f6]"></i> 
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-blue-50 text-[#1e3b8b] p-3.5 rounded-xl transition-transform group-hover:scale-110 shadow-inner">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Total Guru</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_guru'] }} <span class="text-sm font-medium text-gray-400 normal-case tracking-normal">orang</span></h4>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-green-50 text-green-500 p-3.5 rounded-xl transition-transform group-hover:scale-110 shadow-inner">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Hadir Tepat</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['hadir'] }} <span class="text-sm font-medium text-gray-400 normal-case tracking-normal">orang</span></h4>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-orange-50 text-orange-500 p-3.5 rounded-xl transition-transform group-hover:scale-110 shadow-inner">
                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Terlambat</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['terlambat'] }} <span class="text-sm font-medium text-gray-400 normal-case tracking-normal">orang</span></h4>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-red-50 text-red-500 p-3.5 rounded-xl transition-transform group-hover:scale-110 shadow-inner">
                <i class="fa-solid fa-circle-xmark text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-semibold uppercase tracking-wider">Belum Absen</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['alpa'] }} <span class="text-sm font-medium text-gray-400 normal-case tracking-normal">orang</span></h4>
        </div>

    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6 border-b border-gray-50 pb-4">
            <h4 class="text-lg font-bold text-gray-800">Aktivitas Masuk Terbaru</h4>
            <a href="#" class="text-sm text-[#1e3b8b] hover:underline font-semibold">Lihat Semua Data</a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500">
                        <th class="p-4 font-semibold rounded-tl-xl w-16">No</th>
                        <th class="p-4 font-semibold">Nama Guru</th>
                        <th class="p-4 font-semibold">Waktu Masuk</th>
                        <th class="p-4 font-semibold">Status Presensi</th>
                        <th class="p-4 font-semibold text-center rounded-tr-xl">Metode / Jaringan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent_absensi as $index => $absen)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="p-4 text-gray-500 font-medium">{{ $index + 1 }}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($absen->user->name ?? 'G', 0, 2)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-gray-800">{{ $absen->user->name ?? 'Guru Tidak Diketahui' }}</span>
                                    <p class="text-[11px] text-gray-400 font-mono mt-0.5">NIK: {{ $absen->user->nik ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-gray-700 bg-gray-100 px-3 py-1.5 rounded-lg text-xs">
                                <i class="fa-regular fa-clock text-gray-400 mr-1"></i> 
                                {{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }} WIB
                            </span>
                        </td>
                        <td class="p-4">
                            @if($absen->status == 'tepat_waktu')
                                <span class="px-3 py-1.5 bg-green-50 text-green-600 border border-green-100 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Tepat Waktu
                                </span>
                            @elseif($absen->status == 'terlambat')
                                <span class="px-3 py-1.5 bg-orange-50 text-orange-600 border border-orange-100 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    Terlambat
                                </span>
                            @else
                                <span class="px-3 py-1.5 bg-gray-50 text-gray-600 border border-gray-200 rounded-lg text-xs font-bold uppercase tracking-wider">
                                    {{ $absen->status }}
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-1 text-xs font-bold text-green-600">
                                <i class="fa-solid fa-wifi"></i> Valid (LAN)
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fa-regular fa-folder-open text-5xl mb-3 text-gray-200"></i>
                                <p class="font-semibold text-gray-600 text-base">Belum Ada Presensi Masuk</p>
                                <p class="text-sm mt-1">Data absensi hari ini masih kosong.</p>
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