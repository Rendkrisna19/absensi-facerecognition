<div x-show="isSidebarOpen" @click="isSidebarOpen = false" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden" style="display: none;"></div>

<aside :class="{ 
        'translate-x-0': isSidebarOpen, 
        '-translate-x-full': !isSidebarOpen,
        'w-64': !isMini,
        'w-20': isMini
    }" 
    class="fixed inset-y-0 left-0 z-30 flex flex-col transition-all duration-300 transform bg-white text-gray-700 lg:static lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.05)] border-r border-gray-100">
    
    <div class="flex items-center justify-center h-16 border-b border-gray-100 px-4 overflow-hidden shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 object-contain transition-transform duration-300 hover:scale-105">
            <span x-show="!isMini" class="font-bold text-lg text-[#002D8B] whitespace-nowrap transition-opacity duration-300">Tri Jaya</span>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto custom-scrollbar">
        
        @if(auth()->check() && auth()->user()->role === 'admin')
            
            <p x-show="!isMini" class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-4">Menu Utama</p>

            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Dashboard">
                <i class="fa-solid fa-chart-pie text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Dashboard</span>
            </a>

            <a href="{{ route('admin.user.index') }}" 
               class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('admin.user.*') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Manajemen Pengguna">
                <i class="fa-solid fa-user-shield text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Data Pengguna</span>
            </a>
            
            <a href="{{ route('admin.guru.index') }}" 
               class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('admin.guru.*') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Data Guru">
                <i class="fa-solid fa-users text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Data Guru</span>
            </a>

            <a href="{{ route('admin.face.index') }}" 
               class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('admin.face.*') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Perekaman Wajah">
                <i class="fa-solid fa-id-badge text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Perekaman Wajah</span>
            </a>

            <p x-show="!isMini" class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-6">Konfigurasi</p>

            <a href="{{ route('admin.pengaturan-lan.index') }}" class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('admin.pengaturan-lan.*') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Pengaturan Sistem">
                <i class="fa-solid fa-network-wired text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Pengaturan LAN</span>
            </a>

            <a href="{{ route('admin.pengaturan-absensi.index') }}" class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('admin.pengaturan-absensi.*') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Pengaturan Absensi">
                <i class="fa-solid fa-business-time text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Pengaturan Absensi</span>
            </a>
        @endif

        @if(auth()->check() && auth()->user()->role === 'kepala_yayasan')
            
            <p x-show="!isMini" class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 mt-4">Pemantauan</p>

            <a href="{{ route('yayasan.dashboard') }}" 
               class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('yayasan.dashboard') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Dashboard">
                <i class="fa-solid fa-chart-line text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Dashboard</span>
            </a>

            <a href="{{ route('yayasan.laporan.index') }}" class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('yayasan.laporan.*') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Laporan Kehadiran">
                <i class="fa-solid fa-file-signature text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Laporan Kehadiran</span>
            </a>

            <a href="#" class="flex items-center px-3 py-3 rounded-xl group transition-all duration-200 {{ request()->routeIs('yayasan.potongan.*') ? 'bg-[#002D8B] text-white shadow-md' : 'text-gray-500 hover:text-white hover:bg-[#002D8B]' }}" title="Rekap Pemotongan Gaji">
                <i class="fa-solid fa-money-bill-transfer text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Potongan Gaji</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-gray-100 shrink-0 bg-gray-50">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center w-full px-3 py-3 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-colors duration-200 shadow-sm" title="Keluar">
                <i class="fa-solid fa-arrow-right-from-bracket text-lg min-w-[24px] text-center"></i>
                <span x-show="!isMini" class="ml-3 font-medium whitespace-nowrap">Keluar</span>
            </button>
        </form>
    </div>
</aside>

<style>
    /* Agar scrollbar sidebar rapi dan tidak mengganggu UI */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent; 
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e5e7eb; 
        border-radius: 10px;
    }
    .custom-scrollbar:hover::-webkit-scrollbar-thumb {
        background: #d1d5db; 
    }
</style>