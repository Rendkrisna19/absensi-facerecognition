@extends('layouts.app')

@section('title', 'Pengaturan Absensi')
@section('page_title', 'Jam Operasional & Denda')

@section('content')
<div class="w-full space-y-6">
    
    @if(session('success'))
        <div class="bg-green-50 text-green-700 p-4 rounded-xl flex items-center border border-green-200 shadow-sm animate-fade-in-down">
            <i class="fa-solid fa-circle-check text-xl mr-3"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header Card -->
        <div class="bg-gray-50/80 px-6 md:px-8 py-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-brand/10 text-brand flex items-center justify-center text-2xl shadow-inner shrink-0">
                <i class="fa-solid fa-business-time"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-800">Aturan Jam Kerja & Potongan Gaji</h3>
                <p class="text-sm text-gray-500 mt-1">Sistem Face Recognition akan menyesuaikan waktu buka/tutup kamera dan kalkulasi denda berdasarkan jadwal ini.</p>
            </div>
        </div>

        <!-- Form Body -->
        <form action="{{ route('admin.pengaturan-absensi.store') }}" method="POST" class="p-6 md:p-8">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- KIRI: Pengaturan Waktu (Mengambil 2 kolom di layar besar) -->
                <div class="lg:col-span-2 space-y-6">
                    <h4 class="font-bold text-gray-800 text-lg flex items-center gap-2 pb-2 border-b border-gray-100">
                        <i class="fa-regular fa-clock text-brand"></i> Pengaturan Waktu
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jam Buka Absen -->
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-300 transition-colors">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Scanner Masuk Dibuka</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-door-open"></i>
                                </div>
                                <input type="time" name="jam_buka_absen" value="{{ \Carbon\Carbon::parse($pengaturan->jam_buka_absen)->format('H:i') }}" required class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand font-mono text-gray-700 shadow-sm">
                            </div>
                            <p class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-info-circle mr-1 text-blue-400"></i>Guru baru bisa absen mulai jam ini.</p>
                        </div>

                        <!-- Batas Jam Masuk (Telat) -->
                        <div class="bg-red-50/50 p-4 rounded-xl border border-red-100 hover:border-red-300 transition-colors">
                            <label class="block text-sm font-semibold text-red-700 mb-2">Batas Keterlambatan</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-red-400">
                                    <i class="fa-solid fa-user-clock"></i>
                                </div>
                                <input type="time" name="batas_jam_masuk" value="{{ \Carbon\Carbon::parse($pengaturan->batas_jam_masuk)->format('H:i') }}" required class="w-full pl-10 pr-4 py-2.5 bg-white border border-red-200 text-red-700 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 font-mono font-bold shadow-sm">
                            </div>
                            <p class="text-xs text-red-500 mt-2"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Absen lewat jam ini = Terlambat.</p>
                        </div>

                        <!-- Jam Pulang -->
                        <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100 hover:border-gray-300 transition-colors md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Boleh Pulang</label>
                            <div class="relative w-full md:w-1/2">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                                    <i class="fa-solid fa-person-walking-arrow-right"></i>
                                </div>
                                <input type="time" name="jam_pulang" value="{{ \Carbon\Carbon::parse($pengaturan->jam_pulang)->format('H:i') }}" required class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand font-mono text-gray-700 shadow-sm">
                            </div>
                            <p class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-info-circle mr-1 text-blue-400"></i>Scanner absen pulang dibuka mulai jam ini.</p>
                        </div>
                    </div>
                </div>

                <!-- KANAN: Pengaturan Denda (Mengambil 1 kolom di layar besar) -->
                <div class="space-y-6">
                    <h4 class="font-bold text-gray-800 text-lg flex items-center gap-2 pb-2 border-b border-gray-100">
                        <i class="fa-solid fa-money-bill-wave text-brand"></i> Pengaturan Denda
                    </h4>
                    
                    <div class="bg-orange-50/50 p-5 rounded-xl border border-orange-100 h-full flex flex-col justify-start">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">Nominal Denda Keterlambatan</label>
                        <div class="relative mb-3">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none font-bold text-gray-600 bg-gray-100 border-r border-gray-200 rounded-l-lg px-4 h-full">
                                Rp
                            </div>
                            <input type="number" name="denda_terlambat" value="{{ $pengaturan->denda_terlambat }}" required class="w-full pl-16 pr-4 py-3 text-lg font-bold bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-brand focus:border-brand shadow-sm text-gray-800">
                        </div>
                        
                        <div class="flex items-start gap-3 mt-2 bg-white/60 p-3 rounded-lg border border-orange-200/50">
                            <i class="fa-solid fa-circle-info text-orange-500 mt-0.5"></i>
                            <p class="text-xs text-gray-600 leading-relaxed">
                                Sistem memberlakukan <strong>Denda Flat</strong>. Jika guru absen melewati batas jam masuk, nominal ini akan dipotong 1x pada rekapitulasi gaji bulanan.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Area Tombol -->
            <div class="flex justify-end pt-6 mt-8 border-t border-gray-100">
                <button type="submit" class="bg-brand hover:bg-brand-dark text-white font-semibold py-3 px-8 rounded-xl transition-all shadow-md hover:shadow-lg flex items-center group">
                    <i class="fa-solid fa-save mr-2 group-hover:scale-110 transition-transform"></i> Simpan Pengaturan
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection