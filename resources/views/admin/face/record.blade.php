@extends('layouts.app')

@section('title', 'Perekaman Wajah')
@section('page_title', 'Registrasi Wajah Guru')

@section('content')
<div class="max-w-4xl mx-auto">
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <div>
                <h3 class="text-lg font-bold text-gray-800">Perekaman Wajah: {{ $guru->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">NIK: {{ $guru->nik }}</p>
            </div>
            <a href="{{ route('admin.face.index') }}" class="text-gray-500 hover:text-gray-800 transition font-medium text-sm">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>

        <div class="p-6 flex flex-col items-center">
            
            <!-- Area Kamera -->
            <div class="relative w-full max-w-md bg-black rounded-xl overflow-hidden aspect-[3/4] shadow-inner mb-6 border-4 border-gray-100 flex items-center justify-center">
                
                <!-- Indikator Loading Model -->
                <div id="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-black/80 text-white z-20">
                    <i class="fa-solid fa-spinner fa-spin text-4xl mb-3 text-brand-light"></i>
                    <p class="text-sm font-medium animate-pulse">Memuat Model AI...</p>
                </div>

                <!-- Element Video -->
                <video id="video" autoplay muted class="w-full h-full object-cover hidden"></video>
                
                <!-- Canvas untuk menggambar titik wajah (Landmarks) -->
                <canvas id="overlay" class="absolute inset-0 w-full h-full object-cover z-10"></canvas>
            </div>

            <div class="w-full max-w-md text-center space-y-4">
                <p id="status-text" class="text-sm text-gray-500 font-medium">Pastikan wajah berada di tengah kamera dan pencahayaan cukup.</p>
                
                <button id="capture-btn" disabled class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3 px-6 rounded-xl transition shadow-md disabled:bg-gray-300 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-camera mr-2"></i> Rekam & Simpan Wajah
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Load Library secara lokal -->
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const loading = document.getElementById('loading');
    const captureBtn = document.getElementById('capture-btn');
    const statusText = document.getElementById('status-text');

    let stream = null;

    // 1. Load semua model secara lokal
    Promise.all([
        faceapi.nets.ssdMobilenetv1.loadFromUri('/models'),
        faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
        faceapi.nets.faceRecognitionNet.loadFromUri('/models')
    ]).then(startVideo).catch(err => {
        console.error(err);
        Swal.fire('Error', 'Gagal memuat model face-api. Pastikan folder /models sudah ada.', 'error');
    });

    // 2. Hidupkan Kamera
    function startVideo() {
        navigator.mediaDevices.getUserMedia({ video: { width: 720, height: 960 } })
            .then(mediaStream => {
                stream = mediaStream;
                video.srcObject = mediaStream;
                video.classList.remove('hidden');
                loading.classList.add('hidden');
            })
            .catch(err => {
                console.error(err);
                loading.innerHTML = '<i class="fa-solid fa-triangle-exclamation text-red-500 text-4xl mb-3"></i><p class="text-sm text-red-500 text-center px-4">Kamera tidak dapat diakses.<br>Pastikan izin kamera diberikan.</p>';
            });
    }

    // 3. Proses deteksi saat video jalan (Real-time tracking)
    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(overlay, displaySize);

        setInterval(async () => {
            const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
            
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (detection) {
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                // Gambar kerangka wajah
                faceapi.draw.drawDetections(overlay, resizedDetections);
                faceapi.draw.drawFaceLandmarks(overlay, resizedDetections);
                
                captureBtn.disabled = false;
                captureBtn.classList.replace('bg-gray-300', 'bg-brand');
                statusText.innerHTML = '<span class="text-green-600"><i class="fa-solid fa-check-circle mr-1"></i> Wajah terdeteksi! Silakan klik rekam.</span>';
            } else {
                captureBtn.disabled = true;
                statusText.innerHTML = '<span class="text-orange-500"><i class="fa-solid fa-spinner fa-spin mr-1"></i> Mencari wajah...</span>';
            }
        }, 100); // refresh tiap 100ms
    });

    // 4. Tombol Rekam Diklik
    captureBtn.addEventListener('click', async () => {
        captureBtn.disabled = true;
        captureBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Memproses...';

        const detection = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
        
        if (!detection) {
            Swal.fire('Gagal', 'Wajah tidak terdeteksi dengan jelas. Ulangi kembali.', 'error');
            captureBtn.innerHTML = '<i class="fa-solid fa-camera mr-2"></i> Rekam & Simpan Wajah';
            return;
        }

        // Ekstrak array titik wajah jadi text/JSON
        const descriptorArray = Array.from(detection.descriptor);
        const faceData = JSON.stringify(descriptorArray);

        // Kirim ke backend Laravel
        fetch("{{ route('admin.face.store', $guru->id) }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                face_descriptor: faceData
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Matikan kamera
                if(stream) stream.getTracks().forEach(track => track.stop());
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: data.message,
                    icon: 'success',
                    showConfirmButton: false,
                    timer: 2000
                }).then(() => {
                    window.location.href = "{{ route('admin.face.index') }}";
                });
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
        })
        .finally(() => {
            captureBtn.innerHTML = '<i class="fa-solid fa-camera mr-2"></i> Rekam & Simpan Wajah';
            captureBtn.disabled = false;
        });
    });
</script>
@endsection