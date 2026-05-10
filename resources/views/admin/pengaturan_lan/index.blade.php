@extends('layouts.app')

@section('title', 'Pengaturan LAN')
@section('page_title', 'Kelola Jaringan LAN')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    .font-poppins { font-family: 'Poppins', sans-serif !important; }
    
    /* CSS Kustom untuk efek Toggle yang lebih mulus */
    .toggle-checkbox:checked { right: 0; border-color: #22c55e; }
    .toggle-checkbox:checked + .toggle-label { background-color: #22c55e; }
    .toggle-checkbox { right: 0; z-index: 1; border-color: #e5e7eb; transition: all 0.3s; }
    .toggle-label { width: 3rem; background-color: #e5e7eb; border-radius: 9999px; transition: all 0.3s; }
</style>
@endpush

@section('content')
<div class="w-full font-poppins space-y-6">
    
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-800">Daftar IP Jaringan Lokal</h3>
            <p class="text-sm text-gray-500 mt-1">Hanya guru yang terhubung ke jaringan (IP) aktif di bawah ini yang dapat melakukan absensi.</p>
        </div>
        <a href="{{ route('admin.pengaturan-lan.create') }}" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-colors shadow-sm whitespace-nowrap flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Jaringan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm font-medium flex items-center">
            <i class="fa-solid fa-check-circle mr-2 text-lg"></i> {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        
        <form method="GET" action="{{ route('admin.pengaturan-lan.index') }}" class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>Tampilkan</span>
                <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-2 py-1 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                </select>
                <span>Data</span>
            </div>
            
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none bg-white">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Non-Aktif</option>
                </select>
                
                <div class="relative w-full md:w-64">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3b8b] focus:border-[#1e3b8b] block w-full pl-10 p-2" placeholder="Cari Jaringan atau IP...">
                </div>
                
                <button type="submit" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                    Cari
                </button>
            </div>
        </form>

        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-[#243c94] text-white">
                    <tr>
                        <th class="px-5 py-4 font-semibold w-16">No</th>
                        <th class="px-5 py-4 font-semibold">Nama Jaringan</th>
                        <th class="px-5 py-4 font-semibold">IP Address Gateway</th>
                        <th class="px-5 py-4 font-semibold">Keterangan</th>
                        <th class="px-5 py-4 font-semibold text-center w-32">Status Absensi</th>
                        <th class="px-5 py-4 font-semibold text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($ips as $index => $item)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-5 py-4 text-gray-600">{{ $ips->firstItem() + $index }}</td>
                        <td class="px-5 py-4 font-bold text-gray-800">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                    <i class="fa-solid fa-wifi"></i>
                                </div>
                                {{ $item->nama_jaringan }}
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-mono bg-gray-50 text-blue-700 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-bold tracking-wide">
                                {{ $item->ip_address }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $item->keterangan ?? '-' }}</td>
                        
                        <td class="px-5 py-4 text-center">
                            <label class="relative inline-flex items-center justify-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer toggle-status" data-id="{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                            </label>
                            <div class="text-[10px] font-semibold text-gray-400 mt-1 status-label-{{ $item->id }}">
                                {{ $item->is_active ? 'AKTIF' : 'NON-AKTIF' }}
                            </div>
                        </td>

                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.pengaturan-lan.edit', $item->id) }}" class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition-all shadow-sm border border-blue-100 hover:border-transparent" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <button type="button" onclick="confirmDelete({{ $item->id }})" class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-600 hover:text-white transition-all shadow-sm border border-red-100 hover:border-transparent" title="Hapus">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                                <form id="delete-form-{{ $item->id }}" action="{{ route('admin.pengaturan-lan.destroy', $item->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center p-12 text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-network-wired text-5xl mb-4 text-gray-300"></i>
                                <p class="font-bold text-gray-700 text-lg">Belum ada konfigurasi jaringan.</p>
                                <p class="text-sm mt-1">Silakan tambahkan IP/Jaringan yang diizinkan untuk absen.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-100">
            <div class="text-sm text-gray-500">
                Menampilkan <span class="font-bold text-gray-800">{{ $ips->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-800">{{ $ips->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-gray-800">{{ $ips->total() }}</span> data.
            </div>
            <div>
                {{ $ips->links() }}
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- SweetAlert Hapus ---
    function confirmDelete(id) {
        Swal.fire({
            title: '<span class="font-poppins">Hapus Jaringan?</span>',
            html: '<span class="font-poppins text-sm">Jaringan ini akan dihapus dari daftar IP yang diizinkan!</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: '<i class="fa-solid fa-trash mr-1"></i> Ya, Hapus',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'font-poppins rounded-lg',
                cancelButton: 'font-poppins rounded-lg'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }

    // --- AJAX Toggle Switch Aktif/Non-Aktif ---
    document.addEventListener('DOMContentLoaded', function() {
        const toggleCheckboxes = document.querySelectorAll('.toggle-status');
        
        toggleCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const ipId = this.getAttribute('data-id');
                const isChecked = this.checked;
                const labelStatus = document.querySelector(`.status-label-${ipId}`);

                // Animasi teks kecil di bawah toggle
                labelStatus.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

                fetch(`/admin/pengaturan-lan/toggle/${ipId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        // Update teks label
                        labelStatus.innerText = data.is_active ? 'AKTIF' : 'NON-AKTIF';
                        labelStatus.className = data.is_active ? `text-[10px] font-semibold text-green-500 mt-1 status-label-${ipId}` : `text-[10px] font-semibold text-gray-400 mt-1 status-label-${ipId}`;
                        
                        // Opsional: Tampilkan Toast Notifikasi Kecil
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true,
                        });
                        Toast.fire({
                            icon: 'success',
                            title: '<span class="font-poppins text-sm">' + data.message + '</span>'
                        });
                    } else {
                        // Jika gagal dari server, balikkan toggle
                        this.checked = !isChecked;
                        labelStatus.innerText = !isChecked ? 'AKTIF' : 'NON-AKTIF';
                        Swal.fire('Error', 'Gagal merubah status', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Jika jaringan putus/error, balikkan toggle
                    this.checked = !isChecked;
                    labelStatus.innerText = !isChecked ? 'AKTIF' : 'NON-AKTIF';
                    Swal.fire('Error', 'Terjadi kesalahan jaringan', 'error');
                });
            });
        });
    });
</script>
@endpush