@extends('layouts.app')

@section('title', 'Pengaturan LAN')
@section('page_title', 'Kelola Jaringan LAN')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-800">Daftar IP Jaringan Lokal</h3>
            <p class="text-sm text-gray-500 mt-1">Hanya guru yang terhubung ke IP di bawah ini yang dapat melakukan absensi.</p>
        </div>
        <a href="{{ route('admin.pengaturan-lan.create') }}" class="bg-brand hover:bg-brand-dark text-white px-4 py-2 rounded-lg text-sm font-semibold transition shadow-sm whitespace-nowrap">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Jaringan
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-600 text-sm border-b border-gray-200">
                    <th class="p-3 font-medium rounded-tl-lg">Nama Jaringan</th>
                    <th class="p-3 font-medium">IP Address</th>
                    <th class="p-3 font-medium">Status</th>
                    <th class="p-3 font-medium">Keterangan</th>
                    <th class="p-3 font-medium text-center rounded-tr-lg">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($ips as $item)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="p-3 font-semibold text-gray-800">
                        <i class="fa-solid fa-wifi text-gray-400 mr-2"></i> {{ $item->nama_jaringan }}
                    </td>
                    <td class="p-3">
                        <span class="font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded border border-gray-200 text-xs">
                            {{ $item->ip_address }}
                        </span>
                    </td>
                    <td class="p-3">
                        @if($item->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-md text-xs font-semibold">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-xs font-semibold">Non-Aktif</span>
                        @endif
                    </td>
                    <td class="p-3 text-gray-500">{{ $item->keterangan ?? '-' }}</td>
                    <td class="p-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.pengaturan-lan.edit', $item->id) }}" class="w-8 h-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button type="button" onclick="confirmDelete({{ $item->id }})" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition" title="Hapus">
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
                    <td colspan="5" class="text-center p-10 text-gray-400">
                        <i class="fa-solid fa-network-wired text-4xl mb-3"></i>
                        <p>Belum ada konfigurasi jaringan LAN.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Jaringan?',
            text: "Jaringan ini akan dihapus dari daftar IP yang diizinkan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection