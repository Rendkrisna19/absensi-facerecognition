<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tri Jaya System')</title>
    
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#002D8B', 
                            light: '#1A4BAF',
                            dark: '#001A52',
                        }
                    }
                }
            }
        }
        function realtimeClock() {
            return {
                time: '',
                date: '',
                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                },
                updateClock() {
                    const now = new Date();
                    const optionsDate = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                    const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
                    
                    this.date = now.toLocaleDateString('id-ID', optionsDate);
                    this.time = now.toLocaleTimeString('id-ID', optionsTime) + ' WIB';
                }
            }
        }
    </script>

    <!-- Alpine.js untuk state Sidebar -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ isSidebarOpen: false, isMini: false }">
    
    <div class="flex h-screen overflow-hidden">
        
        <!-- Panggil Komponen Shared Sidebar -->
        @include('components.sidebar')

        <div class="flex flex-col flex-1 w-full transition-all duration-300">
            <!-- Panggil Komponen Shared Header -->
            @include('components.header')

            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-100">
                @yield('content')
            </main>

            <!-- Panggil Komponen Shared Footer -->
            @include('components.footer')
        </div>
    </div>

    @if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        timer: 2500,
        showConfirmButton: false
    });
</script>
@endif

@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "{{ session('error') }}",
    });
</script>
@endif

</body>
</html>