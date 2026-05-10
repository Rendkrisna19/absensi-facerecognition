@extends('layouts.app') 
@section('title', 'Edit Akun Pengguna')

@push('styles')
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8 font-poppins">
    
    <div class="mb-8 border-b border-gray-100 pb-4">
        <h4 class="text-xl font-bold text-gray-800">Edit Akun Pengguna</h4>
        <p class="text-sm text-gray-500 mt-1">Perbarui informasi profil dan hak akses pengguna di bawah ini.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.user.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all" required placeholder="Masukkan nama lengkap">
            </div>

            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username / NIK</label>
                <input type="text" name="username" id="username" value="{{ old('username', $user->username) }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all" required placeholder="Masukkan NIK atau Username">
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Hak Akses (Role)</label>
                <select name="role" id="role" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all" required>
                    <option value="guru" {{ (old('role', $user->role) == 'guru') ? 'selected' : '' }}>Guru</option>
                    <option value="admin" {{ (old('role', $user->role) == 'admin') ? 'selected' : '' }}>Admin</option>
                    <option value="kepala_yayasan" {{ (old('role', $user->role) == 'kepala_yayasan') ? 'selected' : '' }}>Kepala Yayasan</option>
                </select>
            </div>

            <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password Baru (Opsional)</label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all" placeholder="Kosongkan jika tidak ingin mengubah password">
                <p class="text-xs text-gray-500 mt-2"><i class="fa-solid fa-circle-info mr-1"></i> Minimal 6 karakter. Biarkan kosong jika password tetap sama.</p>
            </div>
        </div>

        <div class="mt-8 flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.user.index') }}" class="px-6 py-2.5 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 font-semibold text-sm transition-colors">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl text-white bg-[#1e3b8b] hover:bg-[#152b69] font-semibold text-sm transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection