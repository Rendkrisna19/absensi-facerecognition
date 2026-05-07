@extends('layouts.auth')

@section('title', 'Login - Sistem Absensi Tri Jaya')

@section('content')
<div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">
    <!-- Aksen Warna Atas -->
    <div class="h-2 w-full bg-[#002D8B]"></div>
    
    <div class="p-8">
        <div class="text-center mb-8">
            <!-- Pemasangan Logo Yayasan -->
            <img src="{{ asset('images/logo.png') }}" 
                 alt="Logo Yayasan Tri Jaya" 
                 class="w-24 h-24 mx-auto mb-4 object-contain">
                 
            <h2 class="text-2xl font-bold text-gray-800">Sistem Absensi</h2>
            <p class="text-gray-500 text-sm mt-1">Yayasan Perguruan Tri Jaya</p>
        </div>

        <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
            @csrf
            
            <!-- Input NIK -->
            <div>
                <label for="nik" class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Kependudukan (NIK)</label>
                <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required autofocus autocomplete="off"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#002D8B] focus:border-[#002D8B] outline-none transition {{ $errors->has('nik') ? 'border-red-500' : 'border-gray-300' }}">
                
                @error('nik')
                    <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                @enderror
            </div>

            <!-- Input Password dengan Toggle Mata -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input :type="show ? 'text' : 'password'" name="password" id="password" required
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-[#002D8B] focus:border-[#002D8B] outline-none transition {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }}">
                    
                    <!-- Tombol Icon Mata -->
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-[#002D8B] focus:outline-none transition-colors">
                        <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>
                
                @error('password')
                    <p class="text-red-500 text-xs mt-1 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> {{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Submit -->
            <button type="submit" 
                class="w-full bg-[#002D8B] hover:bg-[#001A52] text-white font-semibold py-2.5 rounded-lg shadow-md hover:shadow-lg transition-all duration-200 mt-2">
                Masuk
            </button>
        </form>
    </div>
</div>
@endsection