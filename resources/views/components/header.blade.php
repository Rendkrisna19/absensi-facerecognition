<header class="flex items-center justify-between h-16 px-6 bg-white shadow-sm border-b border-gray-100 z-10 relative shrink-0">
    <div class="flex items-center">
        <!-- Toggle HP -->
        <button @click="isSidebarOpen = !isSidebarOpen" class="text-gray-500 hover:text-[#002D8B] focus:outline-none lg:hidden transition-colors">
            <i class="fa-solid fa-bars text-xl"></i>
        </button>
        <!-- Toggle Desktop -->
        <button @click="isMini = !isMini" class="hidden text-gray-500 hover:text-[#002D8B] focus:outline-none lg:block transition-colors">
            <i class="fa-solid fa-bars-staggered text-xl"></i>
        </button>
        
        <h2 class="ml-4 text-xl font-bold text-gray-800 hidden sm:block">@yield('page_title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center gap-5">
        
        <!-- Jam Realtime -->
        <div x-data="realtimeClock()" class="hidden md:flex flex-col text-right border-r border-gray-200 pr-4">
            <span class="text-sm font-bold text-[#002D8B]" x-text="time"></span>
            <span class="text-xs text-gray-500" x-text="date"></span>
        </div>

        <!-- Status LAN -->
        <div class="hidden lg:flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-600 border border-green-200 rounded-full text-xs font-bold">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
            LAN Aktif
        </div>

        <!-- Ikon Notifikasi -->
        <button class="relative text-gray-400 hover:text-[#002D8B] transition-colors focus:outline-none">
            <i class="fa-regular fa-bell text-xl"></i>
            <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 border border-white rounded-full"></span>
        </button>

        <!-- Profile Dropdown (Hover State) -->
        <div class="relative" x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false">
            <button class="flex items-center gap-3 focus:outline-none py-2">
                <div class="hidden md:block text-right">
                    <p class="text-sm font-bold text-gray-700 leading-tight">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', auth()->user()->role ?? 'Role') }}</p>
                </div>
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'User') }}&background=002D8B&color=fff&bold=true" alt="Avatar" class="w-10 h-10 rounded-xl shadow-sm object-cover">
                <i class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200" :class="{'rotate-180': dropdownOpen}"></i>
            </button>

            <!-- Menu Dropdown -->
            <div x-show="dropdownOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-2"
                 class="absolute right-0 top-full mt-1 w-48 bg-white rounded-xl overflow-hidden shadow-lg z-99 border border-gray-100 py-1" 
                 style="display: none;">
                
                <a href="#" class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-[#002D8B] transition-colors"><i class="fa-regular fa-user mr-2"></i> Profil Saya</a>
                <a href="#" class="block px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 hover:text-[#002D8B] transition-colors"><i class="fa-solid fa-gear mr-2"></i> Pengaturan</a>
                
                <div class="border-t border-gray-100 my-1"></div>
                
                <form action="{{ route('logout') }}" method="POST" class="block">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket mr-2"></i> Keluar Aplikasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>