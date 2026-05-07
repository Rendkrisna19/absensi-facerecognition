@extends('layouts.app')

@section('title', 'Edit Guru')
@section('page_title', 'Edit Data Guru')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
    
    <!-- Notifikasi Error Validasi -->
    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm border border-red-200">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Update -->
    <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST">
        @csrf
        @method('PUT')

        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">Informasi Akun (Sistem)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $guru->name ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK (Digunakan u/ Login) <span class="text-red-500">*</span></label>
                <input type="number" name="nik" value="{{ old('nik', $guru->nik ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubah Password</label>
                <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand placeholder-gray-400">
                <span class="text-xs text-gray-500 mt-1 block">Hanya diisi jika guru meminta reset password.</span>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="Guru Kelas" {{ old('jabatan', $guru->jabatan ?? '') == 'Guru Kelas' ? 'selected' : '' }}>Guru Kelas</option>
                    <option value="Guru Mata Pelajaran" {{ old('jabatan', $guru->jabatan ?? '') == 'Guru Mata Pelajaran' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                    <option value="Guru BK" {{ old('jabatan', $guru->jabatan ?? '') == 'Guru BK' ? 'selected' : '' }}>Guru BK</option>
                </select>
            </div>
        </div>

        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4">Biodata Lengkap (HRD)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pegawai</label>
                <select name="status_pegawai" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="Honorer" {{ old('status_pegawai', $guru->guru->status_pegawai ?? '') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                    <option value="Tetap" {{ old('status_pegawai', $guru->guru->status_pegawai ?? '') == 'Tetap' ? 'selected' : '' }}>Pegawai Tetap</option>
                    <option value="Kontrak" {{ old('status_pegawai', $guru->guru->status_pegawai ?? '') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
                    <option value="L" {{ old('jenis_kelamin', $guru->guru->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $guru->guru->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $guru->guru->tempat_lahir ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $guru->guru->tanggal_lahir ?? '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Handphone <span class="text-red-500">*</span></label>
                <input type="number" name="no_hp" value="{{ old('no_hp', $guru->guru->no_hp ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                <input type="text" name="pendidikan_terakhir" value="{{ old('pendidikan_terakhir', $guru->guru->pendidikan_terakhir ?? '') }}" placeholder="Contoh: S1 Pendidikan Mtk" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand">{{ old('alamat', $guru->guru->alamat ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.guru.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-lg bg-green-600 text-white font-semibold hover:bg-green-700 transition shadow-md">
                <i class="fa-solid fa-check-double mr-2"></i> Perbarui Data
            </button>
        </div>
    </form>
</div>
@endsection