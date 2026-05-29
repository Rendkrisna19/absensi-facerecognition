<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi Tri Jaya')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
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
                            DEFAULT: '#0a2c9c',
                            light: '#1e40af',
                            dark: '#08227a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        ::selection {
            background-color: #0a2c9c;
            color: #ffffff;
        }
        
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased flex items-center justify-center min-h-screen">
    
    @yield('content')

    @stack('scripts')
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(session('toast_success'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false,
            background: '#002D8B',
            color: '#fff',
            iconColor: '#fff',
            customClass: {
                popup: 'rounded-2xl',
                title: 'font-medium text-sm'
            }
        });
        Toast.fire({
            icon: 'success',
            title: "{{ session('toast_success') }}"
        });
    </script>
    @endif
</body>
</html>