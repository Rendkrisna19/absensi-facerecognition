@extends('layouts.app')

@section('title', 'Dashboard Yayasan')
@section('page_title', 'Dashboard Eksekutif')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="space-y-6">
    
    <div class="bg-gradient-to-r from-[#002D8B] to-[#001f63] rounded-2xl shadow-lg p-6 md:p-8 text-white flex flex-col md:flex-row items-center justify-between relative overflow-hidden">
        <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
        <div class="mb-4 md:mb-0 relative z-5">
            <h2 class="text-2xl font-bold">Ringkasan Manajemen Tri Jaya</h2>
            <p class="text-blue-200 mt-1 text-sm md:text-base">Pantau kedisiplinan dan estimasi pemotongan gaji secara real-time.</p>
        </div>
        
        <form method="GET" class="relative z-10 flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 p-2.5 rounded-xl">
            <select name="bulan" class="bg-white text-gray-800 text-sm font-medium rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300">
                @foreach(range(1, 12) as $bln)
                    <option value="{{ $bln }}" {{ $bulanSelected == $bln ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($bln)->translatedFormat('F') }}
                    </option>
                @endforeach
            </select>
            <select name="tahun" class="bg-white text-gray-800 text-sm font-medium rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-blue-300">
                @foreach(range(date('Y')-2, date('Y')) as $thn)
                    <option value="{{ $thn }}" {{ $tahunSelected == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-white text-[#002D8B] hover:bg-gray-100 px-4 py-2 rounded-lg text-sm font-bold transition-colors shadow-sm">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border-t-4 border-blue-400 shadow-sm relative overflow-hidden group">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Total Guru</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $metrics['total_guru'] }}</h3>
            <i class="fa-solid fa-users absolute -right-3 -bottom-3 text-6xl text-blue-50 group-hover:scale-110 transition-transform"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 border-t-4 border-green-400 shadow-sm relative overflow-hidden group">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Hadir Tepat (Hari Ini)</p>
            <h3 class="text-3xl font-bold text-green-600">{{ $metrics['hadir_tepat'] }}</h3>
            <i class="fa-solid fa-check-circle absolute -right-3 -bottom-3 text-6xl text-green-50 group-hover:scale-110 transition-transform"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 border-t-4 border-yellow-400 shadow-sm relative overflow-hidden group">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Terlambat (Hari Ini)</p>
            <h3 class="text-3xl font-bold text-yellow-500">{{ $metrics['terlambat_hari_ini'] }}</h3>
            <i class="fa-solid fa-clock absolute -right-3 -bottom-3 text-6xl text-yellow-50 group-hover:scale-110 transition-transform"></i>
        </div>
        <div class="bg-white rounded-2xl p-5 border-t-4 border-red-400 shadow-sm relative overflow-hidden group">
            <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mb-1">Belum Absen / Alpa</p>
            <h3 class="text-3xl font-bold text-red-500">{{ $metrics['alpa_hari_ini'] }}</h3>
            <i class="fa-solid fa-user-xmark absolute -right-3 -bottom-3 text-6xl text-red-50 group-hover:scale-110 transition-transform"></i>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-base font-bold text-gray-800">Tren Kehadiran (7 Hari Terakhir)</h4>
            </div>
            <div id="chart-tren" class="w-full h-[300px]"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-base font-bold text-gray-800">5 Absensi Terakhir Hari Ini</h4>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-gray-400 border-b border-gray-100 uppercase text-[10px]">
                            <th class="pb-2 font-medium">Nama & NIK</th>
                            <th class="pb-2 font-medium text-right">Waktu</th>
                            <th class="pb-2 font-medium text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($absenTerakhir as $absen)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3">
                                <p class="font-semibold text-gray-800 text-xs">{{ $absen->user->name }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $absen->user->nik }}</p>
                            </td>
                            <td class="py-3 text-right font-mono text-xs text-gray-600 font-bold">
                                {{ \Carbon\Carbon::parse($absen->jam_masuk)->format('H:i') }}
                            </td>
                            <td class="py-3 text-right">
                                @if($absen->status == 'Terlambat')
                                    <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-[10px] font-bold border border-yellow-200">Telat</span>
                                @else
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold border border-green-200">Tepat</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-gray-400 text-xs">
                                <i class="fa-solid fa-folder-open text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada data absensi hari ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h4 class="text-base font-bold text-gray-800 mb-4 text-center">Komposisi Kehadiran Hari Ini</h4>
            <div id="chart-pie" class="flex justify-center"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 mt-6 mr-4 text-red-50 opacity-50 text-7xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-money-bill-transfer"></i>
            </div>
            <div class="relative z-10 w-full">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Estimasi Denda ({{ $bulanSekarang }})</p>
                <h4 class="text-4xl font-black text-red-600 mb-2">Rp {{ number_format($metrics['total_denda_bulan_ini'], 0, ',', '.') }}</h4>
                <div class="inline-block bg-red-50 text-red-600 text-[10px] font-bold px-3 py-1 rounded-full border border-red-100">
                    Potongan Gaji Yayasan
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex items-center justify-between relative overflow-hidden group">
            <div class="absolute right-0 top-0 mt-6 mr-4 text-green-50 opacity-50 text-7xl group-hover:scale-110 transition-transform">
                <i class="fa-solid fa-chart-line"></i>
            </div>
            <div class="relative z-10 w-full">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rata-rata Kedisiplinan</p>
                <h4 class="text-4xl font-black text-green-500 mb-2">{{ $metrics['rata_kehadiran'] }}%</h4>
                <div class="w-full bg-gray-100 rounded-full h-2 mb-1">
                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $metrics['rata_kehadiran'] }}%"></div>
                </div>
                <p class="text-[10px] text-gray-400">Target kehadiran yayasan: 90%</p>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. AREA CHART (Tren Mingguan)
        var optionsTren = {
            series: [{
                name: 'Hadir Tepat',
                data: @json($chartData['hadir'])
            }, {
                name: 'Terlambat',
                data: @json($chartData['terlambat'])
            }, {
                name: 'Alpa/Belum Hadir',
                data: @json($chartData['alpa'])
            }],
            chart: {
                height: 300,
                type: 'area',
                toolbar: { show: false },
                fontFamily: "'Poppins', sans-serif"
            },
            colors: ['#10b981', '#f59e0b', '#ef4444'], // Hijau, Oranye, Merah
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 2 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            xaxis: {
                categories: @json($chartData['labels']),
                labels: { style: { colors: '#9ca3af', fontSize: '11px', fontWeight: 500 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { colors: '#9ca3af', fontSize: '11px' } }
            },
            grid: { borderColor: '#f3f4f6', strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right', fontSize: '12px', markers: { radius: 12 } },
            tooltip: { theme: 'light' }
        };
        var chartTren = new ApexCharts(document.querySelector("#chart-tren"), optionsTren);
        chartTren.render();

        // 2. DONUT CHART (Komposisi Hari Ini)
        var optionsPie = {
            series: [{{ $metrics['hadir_tepat'] }}, {{ $metrics['terlambat_hari_ini'] }}, {{ $metrics['alpa_hari_ini'] }}],
            labels: ['Tepat Waktu', 'Terlambat', 'Belum Hadir/Alpa'],
            chart: {
                type: 'donut',
                height: 250,
                fontFamily: "'Poppins', sans-serif"
            },
            colors: ['#10b981', '#f59e0b', '#ef4444'],
            plotOptions: {
                pie: {
                    donut: { size: '65%', labels: { show: true, name: { show: false }, value: { fontSize: '24px', fontWeight: 700 } } }
                }
            },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontSize: '12px', markers: { radius: 12 } },
            stroke: { show: true, colors: '#ffffff', width: 2 }
        };
        var chartPie = new ApexCharts(document.querySelector("#chart-pie"), optionsPie);
        chartPie.render();

    });
</script>
@endsection