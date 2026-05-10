@extends('layouts.app') 
@section('title', 'Tambah Akun Baru')

@push('styles')
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="w-full bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 font-poppins">
    
    <div class="mb-8 border-b border-gray-100 pb-4">
        <h4 class="text-xl font-bold text-gray-800">Tambah Akun Baru</h4>
        <p class="text-sm text-gray-500 mt-1">Lengkapi formulir di bawah ini untuk menambahkan pengguna baru ke dalam sistem.</p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm">
            <div class="flex items-center gap-2 font-bold mb-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Terdapat Kesalahan:
            </div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.user.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all placeholder-gray-400" required placeholder="Contoh: Budi Santoso, S.Pd">
            </div>

            <div>
                <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">
                    Username / NIK <span class="text-red-500">*</span>
                </label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all placeholder-gray-400" required placeholder="Masukkan NIK atau Username unik">
            </div>

            <div>
                <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">
                    Hak Akses (Role) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select name="role" id="role" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all appearance-none bg-white" required>
                        <option value="" disabled selected>Pilih Hak Akses</option>
                        <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="kepala_yayasan" {{ old('role') == 'kepala_yayasan' ? 'selected' : '' }}>Kepala Yayasan</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500">
                        <i class="fa-solid fa-chevron-down text-sm"></i>
                    </div>
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="password" class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none transition-all placeholder-gray-400" required placeholder="Masukkan password (minimal 6 karakter)">
            </div>
            
        </div>

        <div class="mt-8 flex flex-col-reverse md:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-100">
            <a href="{{ route('admin.user.index') }}" class="w-full md:w-auto px-6 py-2.5 rounded-xl text-gray-600 bg-gray-100 hover:bg-gray-200 font-semibold text-sm transition-colors text-center">
                Batal
            </a>
            <button type="submit" class="w-full md:w-auto px-6 py-2.5 rounded-xl text-white bg-[#1e3b8b] hover:bg-[#152b69] font-semibold text-sm transition-colors shadow-sm flex justify-center items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Akun
            </button>
        </div>
    </form>
</div>
@endsection