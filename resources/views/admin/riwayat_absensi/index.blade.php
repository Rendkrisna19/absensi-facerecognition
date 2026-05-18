@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('page_title', 'Riwayat Absensi Keseluruhan')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Log Kehadiran Pegawai</h3>
            <p class="text-sm text-gray-500 mt-1">Pantau seluruh riwayat jam masuk, jam pulang, dan status izin pegawai.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.riwayat-absensi.index') }}" class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Cari Pegawai</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] pl-10 p-2.5" placeholder="Nama atau NIK...">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status Kehadiran</label>
            <select name="status" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5">
                <option value="all">Semua Status</option>
                <option value="Hadir" {{ request('status') == 'Hadir' ? 'selected' : '' }}>Hadir Tepat Waktu</option>
                <option value="Terlambat" {{ request('status') == 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                <option value="Alpa" {{ request('status') == 'Alpa' ? 'selected' : '' }}>Alpa (Tanpa Keterangan)</option>
                <option value="Sakit" {{ request('status') == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                <option value="Izin" {{ request('status') == 'Izin' ? 'selected' : '' }}>Izin</option>
                <option value="Cuti" {{ request('status') == 'Cuti' ? 'selected' : '' }}>Cuti</option>
            </select>
        </div>

        <div class="flex gap-2">
            <div class="w-1/2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Bulan</label>
                <select name="bulan" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5">
                    <option value="all">Semua</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                    @endfor
                </select>
            </div>
            <div class="w-1/2">
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tahun</label>
                <select name="tahun" class="w-full bg-white border border-gray-300 text-sm rounded-lg focus:ring-[#002D8B] focus:border-[#002D8B] p-2.5">
                    <option value="all">Semua</option>
                    @for($y=date('Y'); $y>=date('Y')-3; $y--)
                        <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full bg-[#002D8B] hover:bg-[#001f63] text-white px-4 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                <i class="fa-solid fa-filter mr-1"></i> Terapkan Filter
            </button>
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm border-collapse">
            <thead class="bg-[#002D8B] text-white">
                <tr>
                    <th class="p-4 font-semibold w-16">No</th>
                    <th class="p-4 font-semibold">Pegawai</th>
                    <th class="p-4 font-semibold text-center">Tanggal</th>
                    <th class="p-4 font-semibold text-center">Jam Masuk</th>
                    <th class="p-4 font-semibold text-center">Jam Pulang</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($riwayat as $index => $absen)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="p-4 text-gray-600">{{ $riwayat->firstItem() + $index }}</td>
                    <td class="p-4">
                        <div class="font-bold text-gray-800">{{ $absen->user->name ?? 'User Terhapus' }}</div>
                        <div class="text-[11px] text-gray-500">{{ $absen->user->nik ?? '-' }}</div>
                    </td>
                    <td class="p-4 text-center font-medium text-gray-700">
                        {{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d M Y') }}
                    </td>
                    <td class="p-4 text-center font-mono">
                        @if($absen->jam_masuk)
                            <span class="bg-gray-100 px-2 py-1 rounded text-gray-800">{{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }}</span>
                            @if($absen->menit_terlambat > 0)
                                <div class="text-[10px] text-red-500 mt-1 font-sans font-semibold">Telat {{ $absen->menit_terlambat }} mnt</div>
                            @endif
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center font-mono">
                        @if($absen->jam_pulang)
                            <span class="bg-gray-100 px-2 py-1 rounded text-gray-800">{{ \Carbon\Carbon::parse($absen->jam_pulang)->format('H:i') }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="p-4 text-center">
                        @php
                            $badgeClass = 'bg-gray-100 text-gray-600';
                            if($absen->status == 'Hadir') $badgeClass = 'bg-green-100 text-green-700 border-green-200';
                            if($absen->status == 'Terlambat') $badgeClass = 'bg-orange-100 text-orange-700 border-orange-200';
                            if($absen->status == 'Alpa') $badgeClass = 'bg-red-100 text-red-700 border-red-200';
                            if(in_array($absen->status, ['Sakit', 'Izin', 'Cuti'])) $badgeClass = 'bg-blue-100 text-blue-700 border-blue-200';
                        @endphp
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full border {{ $badgeClass }}">
                            {{ $absen->status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center p-12 text-gray-500">
                        <i class="fa-solid fa-calendar-xmark text-4xl mb-3 text-gray-300"></i>
                        <p class="font-bold text-gray-600">Tidak ada riwayat absensi ditemukan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $riwayat->links() }}
    </div>
</div>
@endsection