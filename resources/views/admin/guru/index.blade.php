@extends('layouts.app')

@section('title', 'Data Guru')
@section('page_title', 'Kelola Data Guru')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-bold text-gray-800">Daftar Guru</h3>
        <a href="{{ route('admin.guru.create') }}" class="bg-brand hover:bg-brand-dark text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Guru
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                    <th class="p-3 font-medium">Nama Guru & NIK</th>
                    <th class="p-3 font-medium">Jabatan & Status</th>
                    <th class="p-3 font-medium">Kontak</th>
                    <th class="p-3 font-medium">Status Wajah</th>
                    <th class="p-3 font-medium text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($gurus as $item)
                <!-- Tambahkan x-data alpine untuk state modal per baris -->
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors" x-data="{ detailOpen: false }">
                    <td class="p-3">
                        <div class="font-semibold text-gray-800">{{ $item->name }}</div>
                        <div class="text-xs text-gray-500 mt-0.5"><i class="fa-regular fa-id-card mr-1"></i> {{ $item->nik }}</div>
                    </td>
                    <td class="p-3">
                        <div class="font-medium text-gray-700">{{ $item->jabatan }}</div>
                        <div class="mt-1">
                            <span class="text-[11px] px-2 py-0.5 rounded-full font-medium {{ ($item->guru?->status_pegawai == 'Tetap') ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $item->guru?->status_pegawai ?? 'Belum Diatur' }}
                            </span>
                        </div>
                    </td>
                    <td class="p-3 text-gray-600">
                        <div><i class="fa-solid fa-phone text-xs mr-1"></i> {{ $item->guru?->no_hp ?? '-' }}</div>
                    </td>
                    <td class="p-3">
                        @if($item->face_descriptor)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-600 rounded-md font-semibold text-xs border border-blue-100">
                                <i class="fa-solid fa-face-smile"></i> Terdaftar
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 text-red-500 rounded-md font-semibold text-xs border border-red-100">
                                <i class="fa-solid fa-face-frown"></i> Belum Rekam
                            </span>
                        @endif
                    </td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Tombol Detail (Mata) -->
                            <button @click="detailOpen = true" type="button" class="w-8 h-8 flex items-center justify-center bg-teal-50 text-teal-600 rounded-lg hover:bg-teal-600 hover:text-white transition" title="Lihat Detail">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                            
                            <!-- Tombol Edit -->
                            <a href="{{ route('admin.guru.edit', $item->id) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition" title="Edit Data">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            
                            <!-- Tombol Hapus -->
                            <button type="button" onclick="confirmDelete({{ $item->id }})" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition" title="Hapus Data">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            <form id="delete-form-{{ $item->id }}" action="{{ route('admin.guru.destroy', $item->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>

                    <!-- MODAL DETAIL GURU (Tersembunyi sampai tombol mata diklik) -->
                   <!-- MODAL DETAIL GURU (Dibungkus template teleport) -->
                    <template x-teleport="body">
                        <div x-show="detailOpen" style="display: none;" class="fixed inset-0 z-[99] flex items-center justify-center p-4">
                            <!-- Backdrop -->
                            <div x-show="detailOpen" x-transition.opacity @click="detailOpen = false" class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>

                            <!-- Konten Modal -->
                            <div x-show="detailOpen" 
                                 x-transition.scale.origin.bottom 
                                 class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
                                
                                <!-- Header Modal -->
                                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                        <i class="fa-solid fa-address-card text-brand"></i> Detail Profil Guru
                                    </h3>
                                    <button @click="detailOpen = false" class="text-gray-400 hover:text-red-500 transition-colors">
                                        <i class="fa-solid fa-xmark text-xl"></i>
                                    </button>
                                </div>

                                <!-- Body Modal (Scrollable) -->
                                <div class="p-6 overflow-y-auto">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                                        <!-- Bagian Kiri -->
                                        <div class="space-y-4">
                                            <div>
                                                <p class="text-gray-500 mb-1">Nama Lengkap</p>
                                                <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">NIK</p>
                                                <p class="font-semibold text-gray-800">{{ $item->nik }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Jabatan & Status</p>
                                                <p class="font-semibold text-gray-800">{{ $item->jabatan }} <span class="text-gray-400 font-normal">({{ $item->guru?->status_pegawai ?? 'Belum Diatur' }})</span></p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Jenis Kelamin</p>
                                                <p class="font-semibold text-gray-800">{{ ($item->guru?->jenis_kelamin == 'L') ? 'Laki-laki' : (($item->guru?->jenis_kelamin == 'P') ? 'Perempuan' : '-') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Tempat, Tanggal Lahir</p>
                                                <p class="font-semibold text-gray-800">
                                                    {{ $item->guru?->tempat_lahir ?? '-' }}, 
                                                    {{ $item->guru?->tanggal_lahir ? \Carbon\Carbon::parse($item->guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Bagian Kanan -->
                                        <div class="space-y-4">
                                            <div>
                                                <p class="text-gray-500 mb-1">Nomor Handphone</p>
                                                <p class="font-semibold text-gray-800">{{ $item->guru?->no_hp ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Pendidikan Terakhir</p>
                                                <p class="font-semibold text-gray-800">{{ $item->guru?->pendidikan_terakhir ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Agama</p>
                                                <p class="font-semibold text-gray-800">{{ $item->guru?->agama ?? '-' }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Tanggal Bergabung</p>
                                                <p class="font-semibold text-gray-800">
                                                    {{ $item->guru?->tanggal_bergabung ? \Carbon\Carbon::parse($item->guru->tanggal_bergabung)->translatedFormat('d F Y') : '-' }}
                                                </p>
                                            </div>
                                            <div>
                                                <p class="text-gray-500 mb-1">Status Face Recognition</p>
                                                <p class="font-semibold {{ $item->face_descriptor ? 'text-green-600' : 'text-red-500' }}">
                                                    {{ $item->face_descriptor ? 'Sudah Perekaman Wajah' : 'Belum Ada Data Wajah' }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Baris Penuh Bawah -->
                                        <div class="md:col-span-2 pt-2 border-t border-gray-100">
                                            <p class="text-gray-500 mb-1">Alamat Lengkap</p>
                                            <p class="font-semibold text-gray-800">{{ $item->guru?->alamat ?? '-' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <!-- END MODAL DETAIL -->
                    <!-- END MODAL DETAIL -->

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center p-12">
                        <div class="text-gray-400 mb-2"><i class="fa-solid fa-users-slash text-4xl"></i></div>
                        <p class="text-gray-500 font-medium">Belum ada data guru.</p>
                        <p class="text-sm text-gray-400">Silakan klik tombol Tambah Guru untuk memulai.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Script Pemicu Konfirmasi Hapus (SweetAlert) -->
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data Guru?',
            text: "Data profil dan akun login guru ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Merah Tailwind
            cancelButtonColor: '#6b7280', // Abu-abu Tailwind
            confirmButtonText: '<i class="fa-solid fa-trash-can mr-1"></i> Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection