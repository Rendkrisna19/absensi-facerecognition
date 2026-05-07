@extends('layouts.app')

@section('title', 'Tambah Guru')
@section('page_title', 'Tambah Data Guru')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
    
    <!-- Tampilkan Error Validasi Global -->
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.guru.store') }}" method="POST">
        @csrf

        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">Informasi Akun (Sistem)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK (Digunakan u/ Login) <span class="text-red-500">*</span></label>
                <input type="number" name="nik" value="{{ old('nik') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Default <span class="text-red-500">*</span></label>
                <input type="text" name="password" value="password123" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="">-- Pilih Jabatan --</option>
                    <option value="Guru Kelas">Guru Kelas</option>
                    <option value="Guru Mata Pelajaran">Guru Mata Pelajaran</option>
                    <option value="Guru BK">Guru BK</option>
                </select>
            </div>
        </div>

        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">Biodata Lengkap (HRD)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pegawai</label>
                <select name="status_pegawai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="Honorer">Honorer</option>
                    <option value="Tetap">Pegawai Tetap</option>
                    <option value="Kontrak">Kontrak</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Handphone <span class="text-red-500">*</span></label>
                <input type="number" name="no_hp" value="{{ old('no_hp') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                <input type="text" name="pendidikan_terakhir" placeholder="Contoh: S1 Pendidikan Mtk" value="{{ old('pendidikan_terakhir') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.guru.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-brand text-white font-semibold hover:bg-brand-dark transition shadow-md">
                <i class="fa-solid fa-save mr-2"></i> Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection