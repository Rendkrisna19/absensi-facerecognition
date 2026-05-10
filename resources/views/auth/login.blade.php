@extends('layouts.auth')

@section('title', 'Login - Sistem Absensi Tri Jaya')

@section('content')
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

<div class="flex min-h-screen w-full bg-[#F8FAFC] font-sans items-center justify-center p-4">
    
    <div class="flex flex-col lg:flex-row w-full max-w-5xl bg-white rounded-[32px] shadow-2xl overflow-hidden min-h-[600px]">
        
        <div class="w-full lg:w-1/2 flex items-center justify-center p-10 sm:p-16">
            <div class="w-full">
                
                <div class="flex justify-start mb-8">
                    <lottie-player 
                        src="{{ asset('lottie/logo.json') }}" 
                        background="transparent" 
                        speed="1" 
                        style="width: 70px; height: 70px;" 
                        loop 
                        autoplay>
                    </lottie-player>
                </div>

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Sign in</h2>
                    <p class="text-gray-400 text-sm mt-2 font-medium">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label for="nik" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Email / NIK</label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik') }}" required autofocus
                            class="w-full px-0 py-3 border-b-2 bg-transparent focus:border-[#002D8B] outline-none transition-all duration-300 {{ $errors->has('nik') ? 'border-red-500' : 'border-gray-200' }}"
                            placeholder="Masukkan NIK Anda">
                        
                        @error('nik')
                            <p class="text-red-500 text-xs mt-2 font-medium flex items-center">
                                <i class="fa-solid fa-circle-exclamation mr-1.5"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="password" id="password" required
                                class="w-full px-0 py-3 border-b-2 bg-transparent focus:border-[#002D8B] outline-none transition-all duration-300 {{ $errors->has('password') ? 'border-red-500' : 'border-gray-200' }}"
                                placeholder="••••••••">
                            
                            <button type="button" @click="show = !show" 
                                class="absolute right-0 top-3 text-gray-400 hover:text-[#002D8B] transition-colors">
                                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        
                        @error('password')
                            <p class="text-red-500 text-xs mt-2 font-medium flex items-center">
                                <i class="fa-solid fa-circle-exclamation mr-1.5"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" class="rounded border-gray-300 text-[#002D8B] focus:ring-[#002D8B]">
                            <label for="remember" class="ml-2 text-sm text-gray-500 font-medium">Ingat saya</label>
                        </div>
                        <a href="#" class="text-sm font-bold text-[#002D8B] hover:underline">Lupa Password?</a>
                    </div>

                    <button type="submit" 
                        class="w-full bg-[#0a2c9c] hover:bg-[#002D8B] text-white font-bold py-4 rounded-xl shadow-lg transition-all duration-300 mt-6 active:scale-95">
                        Sign In
                    </button>
                </form>
            </div>
        </div>

        <div class="hidden lg:flex lg:w-1/2 bg-[#161930] relative items-center justify-center p-12">
            
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-20 -mt-20"></div>
            <div class="absolute bottom-0 left-0 w-40 h-40 bg-white opacity-5 rounded-full -ml-10 -mb-10"></div>

            <div class="relative z-10 text-center">
                <div class="w-full max-w-[400px] mx-auto mb-10">
                    <lottie-player 
                        src="{{ asset('lottie/animasi.json') }}" 
                        background="transparent" 
                        speed="1" 
                        style="width: 100%; height: auto;" 
                        loop 
                        autoplay>
                    </lottie-player>
                </div>

                <h3 class="text-2xl font-bold text-white mb-3">Introducing new features</h3>
                <p class="text-gray-300 text-sm leading-relaxed max-w-sm mx-auto">
                    Menganalisis absensi harian kini lebih mudah dengan sistem otomatisasi terbaru dari kami.
                </p>

                <div class="flex justify-center space-x-2 mt-8">
                    <div class="w-2 h-2 rounded-full bg-white"></div>
                    <div class="w-2 h-2 rounded-full bg-white opacity-30"></div>
                    <div class="w-2 h-2 rounded-full bg-white opacity-30"></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection