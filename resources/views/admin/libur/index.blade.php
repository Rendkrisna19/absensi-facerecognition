@extends('layouts.app')

@section('title', 'Pengaturan Libur Semester')
@section('page_title', 'Manajemen Libur Semester')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-data="{ openAdd: false }">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Kalender Libur Semester</h2>
            <p class="text-sm text-gray-500">Tentukan rentang tanggal libur agar absensi dinonaktifkan otomatis.</p>
        </div>
        <button @click="openAdd = true" class="bg-[#24429b] text-white px-5 py-2.5 rounded-xl text-sm font-bold flex items-center gap-2 shadow-sm">
            <i class="fa-solid fa-calendar-plus"></i> Tambah Libur
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-600 uppercase text-[10px] tracking-wider border-b">
                    <th class="px-6 py-4 font-bold">Nama Semester / Keterangan</th>
                    <th class="px-6 py-4 font-bold">Mulai</th>
                    <th class="px-6 py-4 font-bold">Selesai</th>
                    <th class="px-6 py-4 font-bold text-center">Status</th>
                    <th class="px-6 py-4 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($liburs as $l)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-800">{{ $l->nama_semester }}</p>
                        <p class="text-xs text-gray-400">{{ $l->keterangan ?? 'Tidak ada catatan' }}</p>
                    </td>
                    <td class="px-6 py-4 font-medium">{{ \Carbon\Carbon::parse($l->tanggal_mulai)->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4 font-medium">{{ \Carbon\Carbon::parse($l->tanggal_selesai)->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        @if(now()->between($l->tanggal_mulai, $l->tanggal_selesai))
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Sedang Berlangsung</span>
                        @else
                            <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest">Selesai/Belum</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.libur.destroy', $l->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus jadwal ini?')">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-gray-400 text-xs italic">Belum ada pengaturan libur semester.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="openAdd" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" x-cloak>
        <div class="bg-white rounded-2xl w-full max-w-md p-6 shadow-2xl">
            <h3 class="text-lg font-bold mb-4">Tambah Rentang Libur</h3>
            <form action="{{ route('admin.libur.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Nama Semester</label>
                        <input type="text" name="nama_semester" required class="w-full border rounded-lg p-2 text-sm" placeholder="Cth: Semester Genap 2026">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" required class="w-full border rounded-lg p-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" required class="w-full border rounded-lg p-2 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Keterangan</label>
                        <textarea name="keterangan" rows="2" class="w-full border rounded-lg p-2 text-sm"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" @click="openAdd = false" class="px-4 py-2 text-sm font-bold text-gray-500">Batal</button>
                    <button type="submit" class="bg-[#24429b] text-white px-4 py-2 rounded-lg text-sm font-bold">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection