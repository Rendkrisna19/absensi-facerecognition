@extends('layouts.mobile')

@section('title', 'Kamera Absensi')
@section('subtitle', 'Absensi Harian')
@section('page_title', 'Scan Wajah')

@section('content')
<div class="flex flex-col items-center w-full h-full">

    @if(!$ipValid)
        <!-- ERROR: BUKAN IP SEKOLAH -->
        <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-3xl text-center w-full mt-4">
            <i class="fa-solid fa-network-wired text-5xl mb-4 text-red-400"></i>
            <h3 class="text-lg font-bold mb-2">Akses Ditolak</h3>
            <p class="text-sm mb-4">Anda tidak terhubung dengan jaringan WiFi / LAN Sekolah. Absensi tidak dapat dilakukan.</p>
            <p class="text-xs font-mono bg-white px-2 py-1 rounded border border-red-100 inline-block">IP Anda: {{ $ipUser }}</p>
        </div>
    @elseif(!$wajahTerdaftar)
        <!-- ERROR: WAJAH BELUM DIREKAM ADMIN -->
        <div class="bg-orange-50 border border-orange-200 text-orange-700 p-6 rounded-3xl text-center w-full mt-4">
            <i class="fa-solid fa-face-frown text-5xl mb-4 text-orange-400"></i>
            <h3 class="text-lg font-bold mb-2">Wajah Belum Terdaftar</h3>
            <p class="text-sm">Silakan hubungi Admin Sekolah untuk melakukan perekaman wajah (Enrollment) terlebih dahulu.</p>
        </div>
    @else
        <!-- KAMERA ABSENSI (IP VALID & WAJAH ADA) -->
        <div class="w-full bg-white p-4 rounded-3xl shadow-sm border border-gray-100 mb-6 text-center">
            <p class="text-xs text-gray-500 font-medium mb-1">Arahkan wajah Anda ke kamera</p>
            
            <div class="relative w-full aspect-[3/4] bg-gray-900 rounded-2xl overflow-hidden shadow-inner border-4 border-gray-50 flex items-center justify-center">
                <!-- Loading State -->
                <div id="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-900 z-20 text-white">
                    <i class="fa-solid fa-spinner fa-spin text-3xl mb-3 text-brand"></i>
                    <p class="text-xs font-medium text-center px-4">Menyiapkan AI Pendeteksi Wajah...<br><span class="text-[10px] text-gray-400">(Pastikan internet Anda stabil)</span></p>
                </div>

                <video id="video" autoplay muted class="w-full h-full object-cover hidden"></video>
                <canvas id="overlay" class="absolute inset-0 w-full h-full object-cover z-10"></canvas>
            </div>
            
            <p id="status-text" class="text-sm font-semibold text-gray-600 mt-4"><i class="fa-solid fa-circle-notch fa-spin text-brand mr-1"></i> Sedang mendeteksi wajah...</p>
        </div>
    @endif

</div>

@push('scripts')
@if($ipValid && $wajahTerdaftar)
<!-- Pastikan file face-api.min.js sudah ada di folder public/js -->
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const loading = document.getElementById('loading');
    const statusText = document.getElementById('status-text');

    // Ambil data wajah dan nama dari database
    const userName = "{{ auth()->user()->name }}";
    const storedDescriptorData = @json(json_decode(auth()->user()->face_descriptor));
    const storedDescriptor = new Float32Array(Object.values(storedDescriptorData));

    let isMatched = false;

    // MENGGUNAKAN MODE LOKAL (Sesuai folder public/models di Laravel)
    const modelUrl = '{{ asset("models") }}';

    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri(modelUrl),
        faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl),
        faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl)
    ]).then(startVideo).catch(err => {
        console.error("Detail Error AI:", err);
        Swal.fire('Error Model Lokal', 'Gagal memuat file AI. Pastikan folder "models" sudah ada di dalam folder "public".', 'error');
    });

    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
            .then(stream => {
                video.srcObject = stream;
                video.classList.remove('hidden');
                loading.classList.add('hidden');
            })
            .catch(err => {
                loading.innerHTML = '<p class="text-xs text-red-500 px-4 text-center">Akses kamera ditolak. Mohon izinkan akses kamera pada browser Anda.</p>';
            });
    }

    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(overlay, displaySize);

        const interval = setInterval(async () => {
            if(isMatched) return; // Hentikan deteksi kalau sudah cocok

            const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
            
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (detection) {
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                
                // Gambar kotak wajah
                const box = resizedDetections.detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: userName, boxColor: 'rgba(0, 45, 139, 0.8)' }); // Menggunakan warna brand
                drawBox.draw(overlay);

                const distance = faceapi.euclideanDistance(detection.descriptor, storedDescriptor);
                
                if (distance < 0.45) {
                    isMatched = true; // Kunci agar tidak ngirim request berkali-kali
                    clearInterval(interval);
                    statusText.innerHTML = '<span class="text-green-600"><i class="fa-solid fa-check-circle"></i> Wajah Cocok! Menyimpan Absensi...</span>';
                    
                    // PROSES SIMPAN KE DATABASE (AJAX)
                    fetch("{{ route('guru.scan.store') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            // Tampilkan notifikasi kecil di pojok tanpa tombol OK, lalu redirect
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Absen Berhasil!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = "{{ route('guru.dashboard') }}";
                            });
                        } else {
                            // Jika sudah absen
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'info',
                                title: 'Informasi',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = "{{ route('guru.dashboard') }}";
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error saat simpan absen:', error);
                        Swal.fire('Error', 'Terjadi kesalahan sistem saat menyimpan data.', 'error');
                        isMatched = false; // Buka kunci kalau error server
                    });

                } else {
                    statusText.innerHTML = '<span class="text-red-500"><i class="fa-solid fa-xmark-circle"></i> Wajah tidak cocok. Coba posisikan lebih baik.</span>';
                }
            } else {
                statusText.innerHTML = '<span class="text-gray-600"><i class="fa-solid fa-circle-notch fa-spin text-brand mr-1"></i> Mencari wajah...</span>';
            }
        }, 500); // Cek setiap setengah detik agar browser tidak ngelag
    });
</script>
@endif
@endpush
@endsection