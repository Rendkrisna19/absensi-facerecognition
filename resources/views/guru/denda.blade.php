@extends('layouts.mobile')

@section('title', 'Informasi Denda')
@section('subtitle', 'Estimasi Pemotongan Gaji')
@section('page_title', 'Denda Saya')

@section('content')
<div class="space-y-5">

    <!-- Card Total Denda (Dinamis Sesuai Filter) -->
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-3xl p-6 text-white shadow-[0_10px_25px_rgba(239,68,68,0.3)] relative overflow-hidden text-center">
        <div class="absolute -left-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative z-10">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm shadow-inner">
                <i class="fa-solid fa-money-bill-transfer text-3xl"></i>
            </div>
            <p class="text-red-100 text-sm font-medium mb-1">Total Denda ({{ $namaBulanTahun }})</p>
            <h2 class="text-4xl font-bold mb-3">Rp {{ number_format($totalDenda, 0, ',', '.') }}</h2>
            
            <div class="inline-block bg-black/10 px-4 py-2 rounded-xl text-xs text-red-50 backdrop-blur-md">
                Terlambat: <strong>{{ $totalHariTelat }} Hari</strong> x Rp {{ number_format($nominalDendaFlat, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <!-- FITUR FILTER BULAN & TAHUN -->
    <form action="{{ route('guru.denda') }}" method="GET" class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100 flex gap-2 items-end mt-2">
        <div class="flex-1">
            <label class="text-[10px] font-semibold text-gray-500 ml-1">Bulan</label>
            <select name="bulan" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-red-500">
                @foreach(range(1, 12) as $bln)
                    <option value="{{ $bln }}" {{ $bulanSelected == $bln ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($bln)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="flex-1">
            <label class="text-[10px] font-semibold text-gray-500 ml-1">Tahun</label>
            <select name="tahun" class="w-full bg-gray-50 border border-gray-200 text-gray-700 text-xs rounded-xl px-3 py-2 focus:outline-none focus:border-red-500">
                @foreach(range(date('Y')-1, date('Y')+1) as $thn)
                    <option value="{{ $thn }}" {{ $tahunSelected == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl text-xs font-bold transition-colors h-[34px]">
            <i class="fa-solid fa-filter"></i> Filter
        </button>
    </form>

    <h3 class="font-bold text-gray-800 ml-1 pt-2">Rincian Keterlambatan</h3>

    <!-- List Hari Terlambat -->
    <div class="space-y-3">
        @forelse($riwayatTerlambat as $absen)
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-red-100 flex items-center justify-between relative overflow-hidden">
                <!-- Pita merah kecil di kiri -->
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                
                <div class="flex items-center gap-4 pl-2">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center font-bold shadow-inner">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($absen->tanggal)->translatedFormat('d F Y') }}</p>
                        
                        <!-- Logika Ubah Menit ke Jam & Menit -->
                        @php
                            $jam = floor($absen->menit_terlambat / 60);
                            $menit = $absen->menit_terlambat % 60;
                            $teksTelat = '';
                            if($jam > 0) $teksTelat .= $jam . ' Jam ';
                            if($menit > 0) $teksTelat .= $menit . ' Mnt';
                            if($jam == 0 && $menit == 0) $teksTelat = '0 Mnt';
                        @endphp

                        <p class="text-xs text-gray-500 mt-0.5 font-medium">Masuk: <span class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }} WIB</span> <span class="text-red-500 ml-1">(Telat {{ $teksTelat }})</span></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2.5 py-1 bg-red-50 text-red-600 rounded-md text-[10px] font-bold">
                        + Rp {{ number_format($nominalDendaFlat, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        @empty
            <div class="bg-green-50 rounded-2xl p-6 shadow-sm border border-green-100 text-center mt-2">
                <i class="fa-solid fa-face-smile-beam text-4xl text-green-400 mb-3"></i>
                <h4 class="text-green-800 font-bold mb-1">Luar Biasa!</h4>
                <p class="text-xs text-green-600">Tidak ada catatan keterlambatan pada bulan ini. Pertahankan!</p>
            </div>
        @endforelse
    </div>

    <!-- Spacer agar list terbawah tidak tertutup navigasi -->
    <div class="h-8"></div>
</div>z`
@endsection