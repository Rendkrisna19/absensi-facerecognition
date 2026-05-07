@extends('layouts.app')

@section('title', 'Laporan Kehadiran')
@section('page_title', 'Laporan Kehadiran Guru')

@section('content')
<div class="space-y-6">

    <!-- Card Filter Pencarian -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gray-50/50 px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand/10 text-brand flex items-center justify-center">
                    <i class="fa-solid fa-filter"></i>
                </div>
                Filter Laporan
            </h4>
        </div>
        
        <div class="p-6">
            <form action="{{ route('yayasan.laporan.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
                    
                    <!-- Filter Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dari Tanggal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-regular fa-calendar"></i>
                            </div>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand focus:bg-white transition-all text-sm">
                        </div>
                    </div>
                    
                    <!-- Filter Tanggal Akhir -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sampai Tanggal</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-regular fa-calendar-check"></i>
                            </div>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand focus:bg-white transition-all text-sm">
                        </div>
                    </div>

                    <!-- Filter Nama Guru -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Guru</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <select name="guru_id" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand focus:bg-white transition-all text-sm appearance-none">
                                <option value="">-- Semua Guru --</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="flex gap-2 h-[42px]">
                        <button type="submit" class="flex-1 bg-brand hover:bg-brand-dark text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i class="fa-solid fa-magnifying-glass"></i> Tampilkan
                        </button>
                        @if(request()->anyFilled(['start_date', 'end_date', 'guru_id']))
                        <a href="{{ route('yayasan.laporan.index') }}" class="px-4 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-all flex items-center justify-center" title="Reset Filter">
                            <i class="fa-solid fa-rotate-right"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Tabel Data -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header Tabel & Export -->
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h4 class="font-bold text-gray-800 text-lg">Riwayat Presensi</h4>
                <p class="text-sm text-gray-500 mt-0.5">Menampilkan <span class="font-semibold text-brand">{{ $absensis->count() }}</span> data pada halaman ini</p>
            </div>
            
            <!-- Tempat Tombol Export PDF / Excel (Sesuai Brief) -->
            <div class="flex items-center gap-2">
                <button type="button" class="px-4 py-2 bg-green-50 text-green-700 hover:bg-green-600 hover:text-white rounded-xl text-sm font-semibold transition-colors border border-green-200 hover:border-green-600 flex items-center gap-2">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </button>
                <button type="button" class="px-4 py-2 bg-red-50 text-red-700 hover:bg-red-600 hover:text-white rounded-xl text-sm font-semibold transition-colors border border-red-200 hover:border-red-600 flex items-center gap-2">
                    <i class="fa-solid fa-file-pdf"></i> Cetak PDF
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                        <th class="px-6 py-4 font-semibold">Tanggal</th>
                        <th class="px-6 py-4 font-semibold">Profil Guru</th>
                        <th class="px-6 py-4 font-semibold text-center">Jam Masuk</th>
                        <th class="px-6 py-4 font-semibold text-center">Keterlambatan</th>
                        <th class="px-6 py-4 font-semibold text-center">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($absensis as $absen)
                    <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('l') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <!-- Inisial Profil -->
                                @php
                                    $nameParts = explode(' ', $absen->user->name ?? 'U T');
                                    $initials = substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : '');
                                @endphp
                                <div class="w-10 h-10 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold text-sm border border-brand/20 group-hover:bg-brand group-hover:text-white transition-colors">
                                    {{ strtoupper($initials) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800">{{ $absen->user->name ?? 'User Terhapus' }}</div>
                                    <div class="text-xs text-gray-500">{{ $absen->user->jabatan ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-mono bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg border border-gray-200 text-xs">
                                {{ $absen->jam_masuk ? \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') : '--:--' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($absen->menit_terlambat > 0)
                                <span class="inline-flex items-center justify-center bg-red-50 text-red-600 border border-red-100 px-2.5 py-1 rounded-lg font-bold text-xs">
                                    +{{ $absen->menit_terlambat }} Menit
                                </span>
                            @else
                                <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($absen->status == 'Hadir')
                                <span class="inline-flex items-center px-3 py-1 bg-green-50 border border-green-200 text-green-700 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Hadir
                                </span>
                            @elseif($absen->status == 'Terlambat')
                                <span class="inline-flex items-center px-3 py-1 bg-orange-50 border border-orange-200 text-orange-700 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5"></span> Terlambat
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 bg-red-50 border border-red-200 text-red-700 rounded-full text-xs font-bold shadow-sm">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Alpa
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                <i class="fa-regular fa-folder-open text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-lg font-bold text-gray-800 mb-1">Data Tidak Ditemukan</h3>
                            <p class="text-sm text-gray-500">Tidak ada riwayat absensi untuk kriteria filter yang dipilih.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($absensis->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $absensis->links() }}
        </div>
        @endif
    </div>
</div>
@endsection