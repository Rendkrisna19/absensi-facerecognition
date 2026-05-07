@extends('layouts.app')

@section('title', 'Tambah LAN')
@section('page_title', 'Tambah Jaringan LAN')

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
    
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm border border-red-200">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengaturan-lan.store') }}" method="POST">
        @csrf
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jaringan / Lokasi <span class="text-red-500">*</span></label>
                <input type="text" name="nama_jaringan" value="{{ old('nama_jaringan') }}" placeholder="Contoh: WiFi Ruang Guru Utama" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP Address <span class="text-red-500">*</span></label>
                <input type="text" name="ip_address" value="{{ old('ip_address') }}" placeholder="Contoh: 192.168.1.100" required class="w-full font-mono px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                <p class="text-xs text-gray-500 mt-1">Masukkan IP Publik atau IP Lokal Router (Gateway) Yayasan.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Jaringan</label>
                <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Aktif (Diizinkan Absen)</option>
                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand" placeholder="Opsional">{{ old('keterangan') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
            <a href="{{ route('admin.pengaturan-lan.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">Kembali</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand text-white font-semibold hover:bg-brand-dark transition shadow-md">
                <i class="fa-solid fa-save mr-2"></i> Simpan
            </button>
        </div>
    </form>
</div>
@endsection