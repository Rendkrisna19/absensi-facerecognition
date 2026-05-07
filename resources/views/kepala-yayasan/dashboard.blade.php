@extends('layouts.app')

@section('title', 'Dashboard Yayasan')
@section('page_title', 'Dashboard Eksekutif')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Banner Eksekutif -->
    <div class="bg-gradient-to-r from-brand to-brand-dark rounded-2xl shadow-lg p-6 md:p-8 text-white flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h2 class="text-2xl font-bold">Ringkasan Manajemen Tri Jaya</h2>
            <p class="text-brand-light mt-1 text-sm md:text-base">Pantau tingkat kedisiplinan dan estimasi pemotongan gaji secara real-time.</p>
        </div>
        <div class="bg-white/10 backdrop-blur-md border border-white/20 px-5 py-2.5 rounded-xl text-center">
            <p class="text-xs text-white/80 uppercase tracking-wider mb-0.5">Bulan Berjalan</p>
            <p class="font-bold text-lg">{{ $bulanSekarang }}</p>
        </div>
    </div>

    <!-- 3 Metrik Utama -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Metrik 1: Terlambat Hari Ini -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Terlambat Hari Ini</p>
                <h4 class="text-3xl font-bold text-gray-800">{{ $metrics['terlambat_hari_ini'] }} <span class="text-base font-normal text-gray-400">Guru</span></h4>
            </div>
            <div class="w-14 h-14 rounded-full bg-orange-50 flex items-center justify-center text-orange-500 text-2xl">
                <i class="fa-solid fa-person-running"></i>
            </div>
        </div>

        <!-- Metrik 2: Total Denda (Penting!) -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="absolute right-0 top-0 mt-4 mr-4 text-red-100 opacity-50 text-5xl">
                <i class="fa-solid fa-rupiah-sign"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-gray-500 mb-1">Total Akumulasi Potongan (Bulan Ini)</p>
                <h4 class="text-3xl font-bold text-red-600">Rp {{ number_format($metrics['total_denda_bulan_ini'], 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Metrik 3: Rata-rata Kehadiran -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition-shadow">
            <div>
                <p class="text-sm font-medium text-gray-500 mb-1">Rata-rata Kehadiran Tepat Waktu</p>
                <h4 class="text-3xl font-bold text-green-600">{{ $metrics['rata_kehadiran'] }}%</h4>
            </div>
            <div class="w-14 h-14 rounded-full bg-green-50 flex items-center justify-center text-green-500 text-2xl">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>

    <!-- Area Grafik Kedisiplinan -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h4 class="text-lg font-bold text-gray-800">Tren Kedisiplinan Mingguan</h4>
            <button class="text-sm text-brand font-semibold hover:underline">Lihat Detail Laporan <i class="fa-solid fa-arrow-right ml-1"></i></button>
        </div>
        
        <!-- Canvas untuk Chart.js -->
        <div class="relative w-full h-[300px]">
            <canvas id="kehadiranChart"></canvas>
        </div>
    </div>

</div>

<!-- Load Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Inisialisasi Grafik -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('kehadiranChart').getContext('2d');
        
        // Ambil data dari Controller PHP
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Hadir Tepat Waktu',
                        data: chartData.hadir,
                        backgroundColor: '#10b981', // Emerald 500 (Hijau Tailwind)
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Terlambat',
                        data: chartData.terlambat,
                        backgroundColor: '#f59e0b', // Amber 500 (Oranye Tailwind)
                        borderRadius: 4,
                        borderSkipped: false,
                    },
                    {
                        label: 'Alpa / Tidak Hadir',
                        data: chartData.alpa,
                        backgroundColor: '#ef4444', // Red 500
                        borderRadius: 4,
                        borderSkipped: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { family: "'Poppins', sans-serif" }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        titleFont: { family: "'Poppins', sans-serif" },
                        bodyFont: { family: "'Poppins', sans-serif" }
                    }
                },
                scales: {
                    x: {
                        stacked: true, // Membuat bar menumpuk ke atas (stacked bar chart)
                        grid: { display: false }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        grid: { borderDash: [4, 4] }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    });
</script>
@endsection