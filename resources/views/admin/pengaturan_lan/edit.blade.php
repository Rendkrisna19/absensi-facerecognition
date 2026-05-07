@extends('layouts.app')

@section('title', 'Edit LAN')
@section('page_title', 'Edit Jaringan LAN')

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

    <form action="{{ route('admin.pengaturan-lan.update', $ip->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Jaringan / Lokasi <span class="text-red-500">*</span></label>
                <input type="text" name="nama_jaringan" value="{{ old('nama_jaringan', $ip->nama_jaringan) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">IP Address <span class="text-red-500">*</span></label>
                <input type="text" name="ip_address" value="{{ old('ip_address', $ip->ip_address) }}" required class="w-full font-mono px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Jaringan</label>
                <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="1" {{ old('is_active', $ip->is_active) == '1' ? 'selected' : '' }}>Aktif (Diizinkan Absen)</option>
                    <option value="0" {{ old('is_active', $ip->is_active) == '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">{{ old('keterangan', $ip->keterangan) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-100">
            <a href="{{ route('admin.pengaturan-lan.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition shadow-md">
                <i class="fa-solid fa-check-double mr-2"></i> Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection