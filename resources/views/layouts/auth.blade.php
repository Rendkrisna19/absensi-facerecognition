<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Absensi Tri Jaya')</title>
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome CDN (Penting untuk ikon di input dan modal) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js (Penting untuk fungsi interaktif seperti Modal & Toggle Password) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lottie Player (Penting untuk animasi JSON) -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Color & Font Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            DEFAULT: '#0a2c9c', // Sesuai dengan warna biru gradasi tombol
                            light: '#1e40af',
                            dark: '#08227a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS Tambahan (Opsional untuk mempermanis UX) -->
    <style>
        /* Mengubah warna teks yang di-highlight pengguna */
        ::selection {
            background-color: #0a2c9c;
            color: #ffffff;
        }
        
        /* Menyembunyikan ikon mata bawaan dari browser Edge/IE di input password */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased flex items-center justify-center min-h-screen">
    
    <!-- Area ini akan diisi oleh konten dari login.blade.php -->
    @yield('content')

    <!-- Ruang untuk script tambahan dari child view jika diperlukan nantinya -->
    @stack('scripts')
    
</body>
</html>