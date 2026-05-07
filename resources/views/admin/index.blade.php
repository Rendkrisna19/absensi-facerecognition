@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Ringkasan Hari Ini')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Banner -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}! 👋</h3>
            <p class="text-gray-500 mt-1">Pantau presensi dan kedisiplinan guru hari ini secara real-time.</p>
        </div>
        <div class="hidden md:block">
            <span class="text-brand bg-brand-light/10 px-4 py-2 rounded-lg font-semibold border border-brand/20">
                <i class="fa-regular fa-calendar mr-2"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <!-- 4 Statistik Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <!-- Card: Total Guru -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-blue-50 text-brand p-3 rounded-xl transition-transform group-hover:scale-110">
                <i class="fa-solid fa-users text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Total Guru</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['total_guru'] ?? 0 }} <span class="text-sm font-normal text-gray-400">orang</span></h4>
        </div>

        <!-- Card: Hadir -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-green-50 text-green-600 p-3 rounded-xl transition-transform group-hover:scale-110">
                <i class="fa-solid fa-circle-check text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Hadir Tepat Waktu</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['hadir'] ?? 0 }} <span class="text-sm font-normal text-gray-400">orang</span></h4>
        </div>

        <!-- Card: Terlambat -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-orange-50 text-orange-500 p-3 rounded-xl transition-transform group-hover:scale-110">
                <i class="fa-solid fa-clock-rotate-left text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Terlambat</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['terlambat'] ?? 0 }} <span class="text-sm font-normal text-gray-400">orang</span></h4>
        </div>

        <!-- Card: Alpa -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute right-0 top-0 mt-4 mr-4 bg-red-50 text-red-500 p-3 rounded-xl transition-transform group-hover:scale-110">
                <i class="fa-solid fa-circle-xmark text-2xl"></i>
            </div>
            <p class="text-gray-500 text-sm font-medium">Alpa / Belum Absen</p>
            <h4 class="text-3xl font-bold text-gray-800 mt-2">{{ $stats['alpa'] ?? 0 }} <span class="text-sm font-normal text-gray-400">orang</span></h4>
        </div>

    </div>

    <!-- Tabel Data Dummy Sementara -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h4 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Absensi Terbaru</h4>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                        <th class="p-3 font-medium rounded-tl-lg">Nama Guru</th>
                        <th class="p-3 font-medium">Jam Masuk</th>
                        <th class="p-3 font-medium">Status</th>
                        <th class="p-3 font-medium rounded-tr-lg">Validasi LAN</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center font-bold text-xs">AH</div>
                                <span class="font-medium text-gray-800">Ahmad Hidayat, S.Pd</span>
                            </div>
                        </td>
                        <td class="p-3">07:10 WIB</td>
                        <td class="p-3"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold">Tepat Waktu</span></td>
                        <td class="p-3"><i class="fa-solid fa-check text-green-500 mr-1"></i> Valid</td>
                    </tr>
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold text-xs">SM</div>
                                <span class="font-medium text-gray-800">Siti Maryam, S.Pd</span>
                            </div>
                        </td>
                        <td class="p-3 text-red-500 font-medium">07:22 WIB</td>
                        <td class="p-3"><span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs font-semibold">Terlambat (7m)</span></td>
                        <td class="p-3"><i class="fa-solid fa-check text-green-500 mr-1"></i> Valid</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection