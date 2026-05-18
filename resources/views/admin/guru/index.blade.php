@extends('layouts.app')

@section('title', 'Data Pegawai & Guru')
@section('page_title', 'Kelola Data Pegawai & Guru')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    
    <!-- Header & Action Buttons -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Daftar Pegawai & Guru</h3>
            <p class="text-sm text-gray-500 mt-1">Kelola data guru, kepala sekolah, dan staff lainnya.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Tombol Export Excel -->
            <a href="{{ route('admin.guru.export.excel') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
            <!-- Tombol Export PDF -->
            <a href="{{ route('admin.guru.export.pdf') }}" target="_blank" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
            <!-- Tombol Import Excel -->
            <button x-data @click="$dispatch('open-import-modal')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-file-import"></i> Import Excel
            </button>
            <!-- Tombol Tambah -->
            <a href="{{ route('admin.guru.create') }}" class="bg-blue-800 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    <!-- Filter, Search & Pagination Info (No Reload Controls) -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100 mb-4 gap-4">
        <div class="flex items-center gap-3 w-full md:w-auto">
            <span class="text-sm text-gray-600 font-medium">Tampilkan</span>
            <select id="perPage" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-20">
                <option value="5">5</option>
                <option value="10" selected>10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select>
            <span class="text-sm text-gray-600 font-medium">Data</span>
        </div>

        <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
            <!-- Filter Status Dropdown -->
            <select id="filterStatus" class="border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500 w-full md:w-40">
                <option value="all">Semua Status</option>
                <option value="tetap">Pegawai Tetap</option>
                <option value="honorer">Honorer</option>
                <option value="belum diatur">Belum Diatur</option>
            </select>
            
            <!-- Search Bar -->
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" placeholder="Cari nama atau NIK...">
            </div>
        </div>
    </div>

    <!-- Tabel Data -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left border-collapse" id="dataTable">
            <thead>
                <tr class="bg-blue-800 text-white text-sm border-b border-blue-700 cursor-pointer select-none">
                    <th class="p-3 font-semibold hover:bg-blue-700 transition" onclick="sortTable(0)">Nama & Profil <i class="fa-solid fa-sort ml-1 text-blue-300"></i></th>
                    <th class="p-3 font-semibold hover:bg-blue-700 transition" onclick="sortTable(1)">Jabatan & Status <i class="fa-solid fa-sort ml-1 text-blue-300"></i></th>
                    <th class="p-3 font-semibold">Kontak</th>
                    <th class="p-3 font-semibold hover:bg-blue-700 transition" onclick="sortTable(3)">Status Wajah <i class="fa-solid fa-sort ml-1 text-blue-300"></i></th>
                    <th class="p-3 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm bg-white" id="tableBody">
                @forelse($gurus as $item)
                @php
                    // Logika penentuan warna badge jabatan
                    $jabatanRaw = strtolower($item->jabatan);
                    $badgeJabatan = 'bg-blue-100 text-blue-700 border-blue-200'; // Default
                    if (str_contains($jabatanRaw, 'kepala')) {
                        $badgeJabatan = 'bg-purple-100 text-purple-700 border-purple-200';
                    } elseif (str_contains($jabatanRaw, 'satpam') || str_contains($jabatanRaw, 'security')) {
                        $badgeJabatan = 'bg-slate-700 text-white border-slate-800';
                    } elseif (str_contains($jabatanRaw, 'pegawai') || str_contains($jabatanRaw, 'staff')) {
                        $badgeJabatan = 'bg-teal-100 text-teal-700 border-teal-200';
                    }

                    // Foto Profil Safe URL
                    $fotoUrl = $item->foto_profil ? asset('storage/' . $item->foto_profil) : asset('images/default-avatar.png');
                    $statusPegawai = strtolower($item->guru?->status_pegawai ?? 'belum diatur');
                @endphp

                <!-- Data attribute digunakan oleh Javascript untuk filter & search DOM -->
                <tr class="data-row border-b border-gray-100 hover:bg-blue-50 transition-colors" 
                    x-data="{ detailOpen: false, photoOpen: false }"
                    data-search="{{ strtolower($item->name . ' ' . $item->nik . ' ' . $item->jabatan) }}"
                    data-status="{{ $statusPegawai }}"
                >
                    <!-- Col 1: Profil -->
                    <td class="p-3">
                        <div class="flex items-center gap-3">
                            <!-- Foto bisa diklik untuk buka modal zoom foto -->
                            <img @click.stop="photoOpen = true" src="{{ $fotoUrl }}" alt="Foto {{ $item->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-300 shadow-sm cursor-pointer hover:opacity-80 transition-opacity" title="Klik untuk memperbesar foto">
                            <div>
                                <div class="font-bold text-gray-800">{{ $item->name }}</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-regular fa-id-card mr-1"></i> {{ $item->nik }}</div>
                            </div>
                        </div>
                    </td>

                    <!-- Col 2: Jabatan & Status -->
                    <td class="p-3">
                        <div class="flex flex-col gap-1.5 items-start">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="inline-block text-[11px] px-2 py-0.5 rounded-md font-semibold border {{ $badgeJabatan }}">
                                    {{ strtoupper($item->jabatan) }}
                                </span>
                                
                                <!-- Badge Unit Sekolah -->
                                @if($item->unit_sekolah === 'SD')
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-md font-semibold border bg-red-50 text-red-600 border-red-200">
                                        Unit SD
                                    </span>
                                @elseif($item->unit_sekolah === 'SMP')
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-md font-semibold border bg-[#002D8B]/10 text-[#002D8B] border-[#002D8B]/20">
                                        Unit SMP
                                    </span>
                                @else
                                    <span class="inline-block text-[11px] px-2 py-0.5 rounded-md font-semibold border bg-gray-50 text-gray-600 border-gray-200">
                                        Umum
                                    </span>
                                @endif
                            </div>
                            
                            <div>
                                <span class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ ($item->guru?->status_pegawai == 'Tetap') ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                    Status: {{ $item->guru?->status_pegawai ?? 'Belum Diatur' }}
                                </span>
                            </div>
                        </div>
                    </td>

                    <!-- Col 3: Kontak -->
                    <td class="p-3 text-gray-600">
                        <div class="flex items-center gap-2"><i class="fa-solid fa-phone text-xs text-gray-400"></i> {{ $item->guru?->no_hp ?? '-' }}</div>
                        <div class="flex items-center gap-2 mt-1 text-xs"><i class="fa-solid fa-venus-mars text-gray-400"></i> {{ ($item->guru?->jenis_kelamin == 'L') ? 'Laki-laki' : (($item->guru?->jenis_kelamin == 'P') ? 'Perempuan' : '-') }}</div>
                    </td>

                    <!-- Col 4: Status Wajah -->
                    <td class="p-3">
                        @if($item->face_descriptor)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-600 rounded-md font-semibold text-xs border border-green-200">
                                <i class="fa-solid fa-face-smile text-green-500"></i> Perekaman Selesai
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 text-red-600 rounded-md font-semibold text-xs border border-red-200">
                                <i class="fa-solid fa-face-frown text-red-500"></i> Belum Rekam
                            </span>
                        @endif
                    </td>

                    <!-- Col 5: Aksi -->
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <!-- Tombol Detail -->
                            <button @click="detailOpen = true" type="button" class="w-8 h-8 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg hover:bg-teal-600 hover:text-white transition shadow-sm" title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            
                            <!-- Tombol Cetak PDF Per Guru -->
                            <a href="{{ route('admin.guru.print', $item->id) }}" target="_blank" class="w-8 h-8 flex items-center justify-center bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition shadow-sm" title="Cetak Profil">
                                <i class="fa-solid fa-print"></i>
                            </a>
                            
                            <!-- Tombol Edit -->
                            <a href="{{ route('admin.guru.edit', $item->id) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-800 rounded-lg hover:bg-blue-800 hover:text-white transition shadow-sm" title="Edit Data">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            <!-- Tombol Hapus -->
                            <button type="button" onclick="confirmDelete({{ $item->id }})" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition shadow-sm" title="Hapus Data">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            <form id="delete-form-{{ $item->id }}" action="{{ route('admin.guru.destroy', $item->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>

                    <!-- =========================================
                         MODAL FOTO ZOOM (TELEPORTED)
                    ========================================== -->
                    <template x-teleport="body">
                        <div x-show="photoOpen" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                            <!-- Backdrop Hitam Gelap -->
                            <div x-show="photoOpen" x-transition.opacity @click="photoOpen = false" class="absolute inset-0 bg-black bg-opacity-80 backdrop-blur-sm cursor-pointer"></div>

                            <!-- Konten Foto Besar -->
                            <div x-show="photoOpen" x-transition.scale.origin.center class="relative z-10 max-w-2xl max-h-[90vh] flex flex-col items-center">
                                <button @click="photoOpen = false" class="absolute -top-10 right-0 text-white hover:text-gray-300 transition z-20">
                                    <i class="fa-solid fa-xmark text-3xl"></i>
                                </button>
                                <img src="{{ $fotoUrl }}" alt="Foto Zoom {{ $item->name }}" class="max-w-full max-h-[75vh] object-contain rounded-xl shadow-2xl border-4 border-white">
                                <div class="mt-4 text-center">
                                    <h4 class="text-white text-xl font-bold">{{ $item->name }}</h4>
                                    <p class="text-gray-300">{{ $item->nik }}</p>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- =========================================
                         MODAL DETAIL GURU (TELEPORTED)
                    ========================================== -->
                    <template x-teleport="body">
                        <div x-show="detailOpen" style="display: none;" class="fixed inset-0 z-[99] flex items-center justify-center p-4">
                            <!-- Backdrop -->
                            <div x-show="detailOpen" x-transition.opacity @click="detailOpen = false" class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm"></div>

                            <!-- Konten Modal -->
                            <div x-show="detailOpen" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
                                
                                <!-- Header Modal (Biru) -->
                                <div class="px-6 py-4 border-b flex justify-between items-center bg-blue-800 text-white">
                                    <h3 class="text-lg font-bold flex items-center gap-2">
                                        <i class="fa-solid fa-address-card"></i> Profil Detail Pegawai
                                    </h3>
                                    <button @click="detailOpen = false" class="text-blue-100 hover:text-white transition-colors">
                                        <i class="fa-solid fa-xmark text-2xl"></i>
                                    </button>
                                </div>

                                <!-- Body Modal -->
                                <div class="p-6 overflow-y-auto">
                                    
                                    <!-- Foto Profil Besar di Modal -->
                                    <div class="flex flex-col items-center justify-center mb-6">
                                        <img src="{{ $fotoUrl }}" class="w-32 h-32 rounded-full object-cover border-4 border-blue-100 shadow-lg mb-3">
                                        <h4 class="text-xl font-bold text-gray-800">{{ $item->name }}</h4>
                                        <span class="inline-block mt-1 text-xs px-3 py-1 rounded-full border {{ $badgeJabatan }}">{{ strtoupper($item->jabatan) }}</span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-6 text-sm bg-gray-50 p-5 rounded-xl border border-gray-100">
                                        <div class="space-y-4">
                                            <div>
                                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">NIK</p>
                                                <p class="font-medium text-gray-800">{{ $item->nik }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Jenis Kelamin</p>
                                                <p class="font-medium text-gray-800">{{ ($item->guru?->jenis_kelamin == 'L') ? 'Laki-laki' : (($item->guru?->jenis_kelamin == 'P') ? 'Perempuan' : '-') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Tempat, Tanggal Lahir</p>
                                                <p class="font-medium text-gray-800">
                                                    {{ $item->guru?->tempat_lahir ?? '-' }}, 
                                                    {{ $item->guru?->tanggal_lahir ? \Carbon\Carbon::parse($item->guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="space-y-4">
                                            <div>
                                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Nomor Handphone</p>
                                                <p class="font-medium text-gray-800">{{ $item->guru?->no_hp ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Pendidikan Terakhir</p>
                                                <p class="font-medium text-gray-800">{{ $item->guru?->pendidikan_terakhir ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Agama</p>
                                                <p class="font-medium text-gray-800">{{ $item->guru?->agama ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Tanggal Bergabung</p>
                                                <p class="font-medium text-gray-800">
                                                    {{ $item->guru?->tanggal_bergabung ? \Carbon\Carbon::parse($item->guru->tanggal_bergabung)->translatedFormat('d F Y') : '-' }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <div class="md:col-span-2 pt-3 border-t border-gray-200 mt-2">
                                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Alamat Lengkap</p>
                                            <p class="font-medium text-gray-800">{{ $item->guru?->alamat ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Footer Modal -->
                                <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2">
                                    <a href="{{ route('admin.guru.print', $item->id) }}" target="_blank" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition" title="Cetak Data">
                                        <i class="fa-solid fa-print mr-1"></i> Cetak
                                    </a>
                                    <button @click="detailOpen = false" type="button" class="px-4 py-2 bg-blue-800 rounded-lg text-sm font-semibold text-white hover:bg-blue-700 transition">
                                        Tutup
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </tr>
                @empty
                <tr id="emptyState" class="border-b border-gray-100">
                    <td colspan="5" class="text-center p-12">
                        <div class="text-gray-300 mb-3"><i class="fa-solid fa-users-slash text-5xl"></i></div>
                        <p class="text-gray-600 font-bold text-lg">Belum ada data</p>
                        <p class="text-sm text-gray-400 mt-1">Silakan klik tombol Tambah Data untuk memulai.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- UI Pagination Javascript (Dinamic) -->
    <div class="flex flex-col md:flex-row justify-between items-center mt-6 gap-4" id="paginationControls">
        <div class="text-sm text-gray-600 font-medium">
            Menampilkan <span id="infoStart" class="font-bold text-gray-900">0</span> sampai <span id="infoEnd" class="font-bold text-gray-900">0</span> dari <span id="infoTotal" class="font-bold text-gray-900">0</span> data
        </div>
        <div class="inline-flex rounded-md shadow-sm" id="paginationButtons">
            <!-- Tombol pagination digenerate JS disini -->
        </div>
    </div>

    <!-- Modal Import Excel (AlpineJS) -->
    <div x-data="{ open: false }" @open-import-modal.window="open = true">
        <template x-teleport="body">
            <div x-show="open" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <!-- Backdrop -->
                <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-sm cursor-pointer"></div>

                <!-- Modal Content -->
                <div x-show="open" x-transition.scale.origin.bottom class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden z-10">
                    <div class="px-6 py-4 border-b flex justify-between items-center bg-emerald-600 text-white">
                        <h3 class="text-lg font-bold flex items-center gap-2">
                            <i class="fa-solid fa-file-import"></i> Import Data Excel
                        </h3>
                        <button @click="open = false" type="button" class="text-emerald-100 hover:text-white transition-colors">
                            <i class="fa-solid fa-xmark text-2xl"></i>
                        </button>
                    </div>

                    <form action="{{ route('admin.guru.importExcel') }}" method="POST" enctype="multipart/form-data" class="p-6">
                        @csrf
                        <div class="mb-5">
                            <p class="text-sm text-gray-600 mb-3">Silakan unggah file Excel sesuai dengan format template yang disediakan.</p>
                            <a href="{{ route('admin.guru.downloadTemplate') }}" class="inline-flex items-center gap-2 text-sm text-emerald-600 font-semibold hover:text-emerald-800 mb-4 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 transition">
                                <i class="fa-solid fa-download"></i> Unduh Template Excel
                            </a>
                            
                            <label class="block text-sm font-semibold text-gray-700 mb-2">File Excel (.xlsx)</label>
                            <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 text-sm file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                        </div>
                        
                        <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                            <button type="button" @click="open = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition flex items-center gap-2">
                                <i class="fa-solid fa-cloud-arrow-up"></i> Upload & Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</div>

<!-- ==============================================
     SCRIPT LOGIKA CLIENT-SIDE (NO RELOAD)
     ============================================== -->
<script>
    // Konfirmasi Hapus SweetAlert
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data?',
            text: "Data profil dan akun login pegawai ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', 
            cancelButtonColor: '#6b7280', 
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // ENGINE TABEL "RINGAN" (Search, Filter, Pagination, Sort)
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('tableBody');
        const rows = Array.from(document.querySelectorAll('.data-row'));
        const searchInput = document.getElementById('searchInput');
        const perPageSelect = document.getElementById('perPage');
        const filterStatusSelect = document.getElementById('filterStatus');
        
        let currentPage = 1;
        let perPage = parseInt(perPageSelect.value);
        let filteredRows = [...rows]; // State saat ini
        let sortDirection = false; // false = asc, true = desc

        // Fungsi Induk untuk Update Tampilan Tabel
        function updateTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const statusFilter = filterStatusSelect.value.toLowerCase();

            // 1. Lakukan Filter & Search
            filteredRows = rows.filter(row => {
                const textData = row.getAttribute('data-search');
                const rowStatus = row.getAttribute('data-status');
                
                const matchSearch = textData.includes(searchTerm);
                const matchStatus = (statusFilter === 'all') || (rowStatus === statusFilter);
                
                return matchSearch && matchStatus;
            });

            // 2. Kalkulasi Pagination
            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / perPage);
            if (currentPage > totalPages) currentPage = totalPages || 1;

            const startIdx = (currentPage - 1) * perPage;
            const endIdx = startIdx + perPage;

            // 3. Sembunyikan semua baris HTML
            rows.forEach(row => row.style.display = 'none');

            // 4. Tampilkan hanya yang lolos filter & masuk index halaman ini
            filteredRows.slice(startIdx, endIdx).forEach(row => {
                row.style.display = ''; // Kembalikan ke default display (table-row)
            });

            // 5. Update Teks Info
            document.getElementById('infoStart').innerText = totalRows === 0 ? 0 : startIdx + 1;
            document.getElementById('infoEnd').innerText = Math.min(endIdx, totalRows);
            document.getElementById('infoTotal').innerText = totalRows;

            // 6. Generate Tombol Pagination
            generatePagination(totalPages);
        }

        function generatePagination(totalPages) {
            const container = document.getElementById('paginationButtons');
            container.innerHTML = '';

            if (totalPages <= 1) return; // Sembunyikan jika cuma 1 halaman

            // Tombol Prev
            const btnPrev = document.createElement('button');
            btnPrev.innerHTML = '<i class="fa-solid fa-chevron-left text-xs"></i>';
            btnPrev.className = `px-3 py-2 text-sm font-medium border border-gray-300 rounded-l-lg ${currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
            btnPrev.disabled = currentPage === 1;
            btnPrev.onclick = () => { currentPage--; updateTable(); };
            container.appendChild(btnPrev);

            // Tombol Angka (Sederhana)
            for (let i = 1; i <= totalPages; i++) {
                const btnPage = document.createElement('button');
                btnPage.innerText = i;
                btnPage.className = `px-3 py-2 text-sm font-medium border-t border-b border-gray-300 ${currentPage === i ? 'bg-blue-50 text-blue-800 border-l border-r z-10' : 'bg-white text-gray-700 hover:bg-gray-50 border-l'}`;
                btnPage.onclick = () => { currentPage = i; updateTable(); };
                container.appendChild(btnPage);
            }

            // Tombol Next
            const btnNext = document.createElement('button');
            btnNext.innerHTML = '<i class="fa-solid fa-chevron-right text-xs"></i>';
            btnNext.className = `px-3 py-2 text-sm font-medium border border-gray-300 rounded-r-lg border-l-0 ${currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-gray-50'}`;
            btnNext.disabled = currentPage === totalPages;
            btnNext.onclick = () => { currentPage++; updateTable(); };
            container.appendChild(btnNext);
        }

        // Listener untuk Input & Select
        searchInput.addEventListener('input', () => { currentPage = 1; updateTable(); });
        filterStatusSelect.addEventListener('change', () => { currentPage = 1; updateTable(); });
        perPageSelect.addEventListener('change', (e) => { 
            perPage = parseInt(e.target.value); 
            currentPage = 1; 
            updateTable(); 
        });

        // Global Sort Function (Dipanggil dari th onclick)
        window.sortTable = function(columnIndex) {
            sortDirection = !sortDirection; // Toggle Asc/Desc
            rows.sort((a, b) => {
                let cellA = a.cells[columnIndex].innerText.trim().toLowerCase();
                let cellB = b.cells[columnIndex].innerText.trim().toLowerCase();
                
                if (cellA < cellB) return sortDirection ? 1 : -1;
                if (cellA > cellB) return sortDirection ? -1 : 1;
                return 0;
            });
            // Re-append sorted rows to DOM agar urutannya tetap saat difilter
            rows.forEach(row => tableBody.appendChild(row)); 
            updateTable(); // Segarkan tampilan
        };

        // Init Pertama Kali
        updateTable();
    });
</script>
@endsection