@extends('layouts.app') 
@section('title', 'Manajemen Pengguna')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body, .font-poppins { font-family: 'Poppins', sans-serif !important; }
</style>
@endpush

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 font-poppins">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h4 class="text-xl font-bold text-gray-800">Daftar Akun Sistem</h4>
            <p class="text-sm text-gray-500 mt-1">Kelola hak akses admin, kepala yayasan, dan guru.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.user.create') }}" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-center">
            <i class="fa-solid fa-check-circle mr-2 text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium flex items-center">
            <i class="fa-solid fa-triangle-exclamation mr-2 text-lg"></i> {{ session('error') }}
        </div>
    @endif

    <form method="GET" action="{{ route('admin.user.index') }}" class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
        
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <span>Tampilkan</span>
            <select name="per_page" onchange="this.form.submit()" class="border border-gray-300 rounded-md px-2 py-1 focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span>Data</span>
        </div>
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <select name="role" onchange="this.form.submit()" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-[#1e3b8b] focus:border-[#1e3b8b] outline-none bg-white">
                <option value="">Semua Status</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="kepala_yayasan" {{ request('role') == 'kepala_yayasan' ? 'selected' : '' }}>Yayasan</option>
                <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
            </select>
            
            <div class="relative w-full md:w-64">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#1e3b8b] focus:border-[#1e3b8b] block w-full pl-10 p-2" placeholder="Cari nama atau NIK...">
            </div>
            
            <button type="submit" class="bg-[#1e3b8b] hover:bg-[#152b69] text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                Cari
            </button>
            
            @if(request()->has('search') || request()->has('role'))
            <a href="{{ route('admin.user.index') }}" class="bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded-lg text-sm font-semibold transition-colors border border-red-200" title="Reset Pencarian">
                <i class="fa-solid fa-arrows-rotate"></i>
            </a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="w-full text-left text-sm">
            <thead class="bg-[#243c94] text-white">
                <tr>
                    <th class="px-5 py-4 font-semibold w-16">No <i class="fa-solid fa-sort ml-1 text-white/50 text-xs"></i></th>
                    <th class="px-5 py-4 font-semibold">Nama & Profil <i class="fa-solid fa-sort ml-1 text-white/50 text-xs"></i></th>
                    <th class="px-5 py-4 font-semibold">Username / NIK <i class="fa-solid fa-sort ml-1 text-white/50 text-xs"></i></th>
                    <th class="px-5 py-4 font-semibold">Jabatan & Status <i class="fa-solid fa-sort ml-1 text-white/50 text-xs"></i></th>
                    <th class="px-5 py-4 font-semibold text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($users as $index => $user)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-5 py-4 text-gray-600">{{ $users->firstItem() + $index }}</td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-envelope text-gray-400 mr-1"></i> Terdaftar: {{ $user->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <span class="text-gray-700 font-medium">{{ $user->username }}</span>
                    </td>
                    <td class="px-5 py-4">
                        @if($user->role == 'admin')
                            <span class="bg-purple-100 text-purple-700 border border-purple-200 px-3 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider">Admin</span>
                        @elseif($user->role == 'kepala_yayasan')
                            <span class="bg-blue-100 text-blue-700 border border-blue-200 px-3 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider">Yayasan</span>
                        @else
                            <span class="bg-green-100 text-green-700 border border-green-200 px-3 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider">Guru</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.user.edit', $user->id) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white w-9 h-9 rounded-lg flex items-center justify-center transition-all shadow-sm border border-blue-100 hover:border-transparent" title="Edit Akun">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            @if(auth()->id() !== $user->id)
                            <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="inline-block form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="bg-red-50 text-red-500 hover:bg-red-600 hover:text-white w-9 h-9 rounded-lg flex items-center justify-center transition-all shadow-sm border border-red-100 hover:border-transparent btn-delete" title="Hapus Akun">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                            <p class="font-medium text-gray-600">Tidak ada data ditemukan.</p>
                            <p class="text-sm">Silakan ubah kata kunci pencarian atau filter.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="text-sm text-gray-500">
            Menampilkan <span class="font-bold text-gray-800">{{ $users->firstItem() ?? 0 }}</span> sampai <span class="font-bold text-gray-800">{{ $users->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-gray-800">{{ $users->total() }}</span> data.
        </div>
        <div>
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SWEETALERT DELETE ---
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.form-delete');
                Swal.fire({
                    title: '<span class="font-poppins">Hapus Akun?</span>',
                    html: '<span class="font-poppins text-sm">Akun ini tidak akan bisa login lagi ke dalam sistem. Data tidak dapat dikembalikan!</span>',
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
                        form.submit();
                    }
                });
            });
        });
        
        // Catatan: Script JS pencarian (Client-side) dihapus karena sudah diganti dengan Server-Side
    });
</script>
@endpush