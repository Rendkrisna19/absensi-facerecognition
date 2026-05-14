@extends('layouts.mobile') @section('title', 'Kamera Absensi')
@section('subtitle', 'Absensi Harian')
@section('page_title', 'Scan Wajah')

@section('content')
<div class="flex flex-col items-center w-full h-full">

    {{-- 1. VALIDASI JARINGAN WIFI / IP --}}
    @if(!$ipValid)
        <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-3xl text-center w-full mt-4 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-100 rounded-full blur-xl opacity-50"></div>
            <i class="fa-solid fa-network-wired text-5xl mb-4 text-red-400 relative z-10"></i>
            <h3 class="text-lg font-bold mb-2 relative z-10">Akses Ditolak</h3>
            <p class="text-sm mb-4 relative z-10">Anda tidak terhubung dengan jaringan WiFi Sekolah. Absensi tidak dapat dilakukan dari luar jangkauan.</p>
            <p class="text-xs font-mono bg-white px-3 py-1.5 rounded-lg border border-red-100 inline-block relative z-10 shadow-sm">
                IP Perangkat: <span class="font-bold">{{ $ipUser }}</span>
            </p>
        </div>

    {{-- 2. VALIDASI WAKTU, LIBUR, DAN IZIN (DYNAMIC UI) --}}
    @elseif(!isset($isWaktuAbsen) || !$isWaktuAbsen)
        @php
            // Logika dinamis untuk menentukan ikon dan warna berdasarkan isi pesan dari Controller
            $pesanLower = strtolower($pesanWaktu ?? '');
            
            $icon = 'fa-clock';
            $title = 'Pemberitahuan Waktu';
            $color = 'blue'; // Default untuk masalah jam absen

            if (\Illuminate\Support\Str::contains($pesanLower, ['izin', 'pengajuan', 'sakit', 'cuti'])) {
                $icon = 'fa-envelope-open-text';
                $title = 'Status Izin Aktif';
                $color = 'amber'; // Warna kuning/oranye untuk izin
            } elseif (\Illuminate\Support\Str::contains($pesanLower, ['libur', 'ditutup'])) {
                $icon = 'fa-calendar-day';
                $title = 'Pemberitahuan Libur';
                $color = 'teal'; // Warna tosca untuk libur
            }
        @endphp

        <div class="bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-800 p-6 rounded-3xl text-center w-full mt-4 shadow-sm">
            <div class="relative w-20 h-20 mx-auto mb-4 bg-white rounded-full flex items-center justify-center shadow-inner">
                <i class="fa-solid {{ $icon }} text-4xl text-{{ $color }}-500 animate-pulse"></i>
            </div>
            <h3 class="text-lg font-bold mb-2">{{ $title }}</h3>
            <p class="text-sm mb-5 leading-relaxed">{{ $pesanWaktu ?? 'Saat ini berada di luar jam operasional absensi.' }}</p>
            
            <a href="{{ route('guru.dashboard') }}" class="inline-block bg-{{ $color }}-600 hover:bg-{{ $color }}-700 text-white font-bold py-2.5 px-6 rounded-xl transition-colors text-sm shadow-md active:scale-95">
                <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Beranda
            </a>
        </div>

    {{-- 3. VALIDASI WAJAH BELUM TERDAFTAR --}}
    @elseif(!$wajahTerdaftar)
        <div class="bg-orange-50 border border-orange-200 text-orange-800 p-6 rounded-3xl text-center w-full mt-4 shadow-sm">
            <div class="relative w-20 h-20 mx-auto mb-4 bg-white rounded-full flex items-center justify-center shadow-inner border-2 border-orange-100">
                <i class="fa-solid fa-face-frown text-4xl text-orange-400"></i>
            </div>
            <h3 class="text-lg font-bold mb-2">Wajah Belum Terdaftar</h3>
            <p class="text-sm leading-relaxed">Silakan hubungi Admin / Tata Usaha untuk melakukan perekaman data biometrik wajah (Enrollment) terlebih dahulu.</p>
        </div>

    {{-- 4. TAMPILAN KAMERA ABSENSI (Jika Semua Validasi Lolos) --}}
    @else
        <div class="w-full bg-white p-5 rounded-3xl shadow-sm border border-gray-100 mb-6 text-center">
            <p class="text-xs text-gray-500 font-semibold mb-3 uppercase tracking-wider">Arahkan wajah Anda ke kamera</p>
            
            <div class="relative w-full max-w-[280px] mx-auto aspect-[3/4] bg-gray-900 rounded-2xl overflow-hidden shadow-[0_8px_30px_rgba(0,0,0,0.12)] border-4 border-gray-50 flex items-center justify-center">
                
                <div id="loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-800 z-20 text-white">
                    <i class="fa-solid fa-spinner fa-spin text-4xl mb-4 text-[#002D8B]"></i>
                    <p class="text-xs font-medium text-center px-4 leading-relaxed">
                        Menyiapkan AI Scanner...<br>
                        <span class="text-[10px] text-gray-400 font-normal mt-1 block">Pastikan pencahayaan cukup terang</span>
                    </p>
                </div>

                <video id="video" autoplay muted playsinline class="absolute inset-0 w-full h-full object-cover object-center hidden transform scale-x-[-1]"></video>
                <canvas id="overlay" class="absolute inset-0 w-full h-full object-cover object-center z-10 transform scale-x-[-1]"></canvas>
                
                <div class="absolute inset-0 z-15 pointer-events-none flex items-center justify-center opacity-30">
                    <div class="w-40 h-48 border-2 border-dashed border-white rounded-full"></div>
                </div>
            </div>
            
            <p id="status-text" class="text-sm font-bold text-[#002D8B] mt-5 bg-blue-50 py-2.5 rounded-xl border border-blue-100 shadow-sm">
                <i class="fa-solid fa-circle-notch fa-spin mr-1.5"></i> Sedang mendeteksi wajah...
            </p>
        </div>
    @endif

</div>

@push('scripts')
@if($ipValid && $wajahTerdaftar && isset($isWaktuAbsen) && $isWaktuAbsen)
<script src="{{ asset('js/face-api.min.js') }}"></script>
<script>
    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const loading = document.getElementById('loading');
    const statusText = document.getElementById('status-text');

    const userName = "{{ auth()->user()->name }}";
    const storedDescriptorData = @json(json_decode(auth()->user()->face_descriptor));
    const storedDescriptor = new Float32Array(Object.values(storedDescriptorData));

    let isMatched = false;
    const modelUrl = '{{ asset("models") }}';

    // 1. MENGGUNAKAN MODEL YANG JAUH LEBIH RINGAN (TinyFaceDetector)
    Promise.all([
        faceapi.nets.tinyFaceDetector.loadFromUri(modelUrl),
        faceapi.nets.faceLandmark68Net.loadFromUri(modelUrl),
        faceapi.nets.faceRecognitionNet.loadFromUri(modelUrl)
    ]).then(startVideo).catch(err => {
        console.error("Detail Error AI:", err);
        Swal.fire('Error Model Lokal', 'Gagal memuat file AI. Pastikan folder "models" sudah lengkap.', 'error');
    });

    function startVideo() {
        // Resolusi ideal dikurangi sedikit agar proses rendering di HP tidak membebani RAM
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user", width: { ideal: 480 }, height: { ideal: 640 } } })
            .then(stream => {
                video.srcObject = stream;
                video.classList.remove('hidden');
                loading.classList.add('hidden');
            })
            .catch(err => {
                loading.innerHTML = '<p class="text-xs text-red-500 px-4 text-center"><i class="fa-solid fa-camera-slash text-2xl mb-2"></i><br>Akses kamera ditolak.<br>Mohon izinkan akses kamera (Allow Camera) pada browser Anda.</p>';
            });
    }

    video.addEventListener('play', () => {
        const displaySize = { width: video.videoWidth, height: video.videoHeight };
        faceapi.matchDimensions(overlay, displaySize);

        // 2. MENGGANTI setInterval DENGAN REKURSIF AGAR HP TIDAK PANAS
        async function prosesScan() {
            if(isMatched) return;

            // Menggunakan TinyFaceDetectorOptions dengan inputSize kecil agar super cepat
            const detection = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions({ inputSize: 224 }))
                .withFaceLandmarks()
                .withFaceDescriptor();
            
            const ctx = overlay.getContext('2d');
            ctx.clearRect(0, 0, overlay.width, overlay.height);

            if (detection) {
                const resizedDetections = faceapi.resizeResults(detection, displaySize);
                
                // Gambar Box Pengenalan
                const box = resizedDetections.detection.box;
                const drawBox = new faceapi.draw.DrawBox(box, { label: userName, boxColor: 'rgba(0, 45, 139, 0.8)' });
                drawBox.draw(overlay);

                // Hitung kemiripan (Euclidean Distance)
                const distance = faceapi.euclideanDistance(detection.descriptor, storedDescriptor);
                
                // Semakin kecil distance, semakin mirip (0.45 adalah batas toleransi ideal)
                if (distance < 0.45) {
                    isMatched = true; 
                    statusText.innerHTML = '<span class="text-green-600 font-bold"><i class="fa-solid fa-check-circle text-lg mr-1"></i> Wajah Cocok! Menyimpan...</span>';
                    
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
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'success',
                                title: 'Absen Berhasil!', text: data.message,
                                showConfirmButton: false, timer: 2000
                            }).then(() => {
                                window.location.href = "{{ route('guru.dashboard') }}";
                            });
                        } else {
                            Swal.fire({
                                toast: true, position: 'top-end', icon: 'info',
                                title: 'Informasi', text: data.message,
                                showConfirmButton: false, timer: 2500
                            }).then(() => {
                                window.location.href = "{{ route('guru.dashboard') }}";
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error saat simpan absen:', error);
                        Swal.fire('Error', 'Terjadi kesalahan jaringan saat menyimpan data absensi.', 'error');
                        isMatched = false; 
                        setTimeout(prosesScan, 1000); // Lanjut scan kalau misal server error sementara
                    });
                    return; // Hentikan loop sementara proses request selesai
                } else {
                    statusText.innerHTML = '<span class="text-red-500 font-semibold"><i class="fa-solid fa-xmark-circle mr-1"></i> Wajah tidak cocok. Coba posisikan lebih terang.</span>';
                }
            } else {
                statusText.innerHTML = '<span class="text-[#002D8B]"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i> Mencari wajah...</span>';
            }

            // Jeda 400ms sebelum memproses frame berikutnya (Sangat penting agar browser bisa bernapas dan tidak freeze)
            setTimeout(prosesScan, 400);
        }

        // Mulai scanning berulang
        prosesScan();
    });
</script>
@endif
@endpush
@endsection