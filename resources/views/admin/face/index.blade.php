@extends('layouts.app')

@section('title', 'Perekaman Wajah')
@section('page_title', 'Data Perekaman Wajah Guru')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Daftar Perekaman Wajah (Face Enrollment)</h3>
            <p class="text-sm text-gray-500 mt-1">Pilih guru untuk melakukan perekaman atau pembaruan data biometrik wajah.</p>
        </div>
        
        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-100 text-blue-700 px-4 py-2 rounded-lg text-sm flex items-center shadow-sm">
            <i class="fa-solid fa-circle-info mr-2 text-blue-500"></i>
            <span>Pastikan pencahayaan cukup saat merekam.</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                    <th class="p-3 font-medium rounded-tl-lg">Nama Guru & NIK</th>
                    <th class="p-3 font-medium">Jabatan</th>
                    <th class="p-3 font-medium">Status Wajah</th>
                    <th class="p-3 font-medium text-center rounded-tr-lg">Aksi Perekaman</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($gurus as $item)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="p-3">
                        <div class="font-semibold text-gray-800">{{ $item->name }}</div>
                        <div class="text-xs text-gray-500 mt-0.5"><i class="fa-regular fa-id-card mr-1"></i> {{ $item->nik }}</div>
                    </td>
                    <td class="p-3 text-gray-700">
                        {{ $item->jabatan }}
                    </td>
                    <td class="p-3">
                        @if($item->face_descriptor)
                            <div class="flex items-center gap-2">
                                <span class="relative flex h-3 w-3">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                </span>
                                <span class="text-green-600 font-semibold text-xs">Sudah Terdaftar</span>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                <span class="text-red-500 font-semibold text-xs">Belum Rekam</span>
                            </div>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        @if($item->face_descriptor)
                            <!-- Tombol Update Wajah jika sudah ada -->
                            <a href="{{ route('admin.face.record', $item->id) }}" class="inline-flex items-center justify-center bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white px-3 py-1.5 rounded-lg transition text-xs font-semibold" title="Perbarui Wajah">
                                <i class="fa-solid fa-camera-rotate mr-1.5"></i> Perbarui Wajah
                            </a>
                        @else
                            <!-- Tombol Rekam Wajah Baru -->
                            <a href="{{ route('admin.face.record', $item->id) }}" class="inline-flex items-center justify-center bg-brand text-white hover:bg-brand-dark px-3 py-1.5 rounded-lg transition text-xs font-semibold shadow-sm" title="Mulai Perekaman">
                                <i class="fa-solid fa-camera mr-1.5"></i> Mulai Rekam
                            </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center p-12">
                        <div class="text-gray-400 mb-2"><i class="fa-solid fa-user-xmark text-4xl"></i></div>
                        <p class="text-gray-500 font-medium">Belum ada data guru di sistem.</p>
                        <p class="text-sm text-gray-400">Silakan tambahkan data guru terlebih dahulu melalui menu Data Guru.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection