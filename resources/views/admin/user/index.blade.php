@extends('layouts.app') @section('title', 'Manajemen Pengguna')
@section('page_title', 'Data Akun Pengguna')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h4 class="text-lg font-bold text-gray-800">Daftar Akun Sistem</h4>
            <p class="text-xs text-gray-500">Kelola hak akses admin, kepala yayasan, dan guru.</p>
        </div>
        <a href="{{ route('admin.user.create') }}" class="bg-[#002D8B] hover:bg-[#001f63] text-white px-4 py-2 rounded-xl text-sm font-bold transition-colors shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Akun
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium">
            <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm font-medium">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 text-gray-500 uppercase text-[10px] tracking-wider">
                    <th class="px-4 py-3 rounded-tl-xl font-bold">No</th>
                    <th class="px-4 py-3 font-bold">Nama Lengkap</th>
                    <th class="px-4 py-3 font-bold">Username / NIK</th>
                    <th class="px-4 py-3 font-bold">Role Akses</th>
                    <th class="px-4 py-3 rounded-tr-xl font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 text-gray-600">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-bold text-gray-800">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-gray-600 font-mono">{{ $user->username }}</td>
                    <td class="px-4 py-3">
                        @if($user->role == 'admin')
                            <span class="bg-purple-100 text-purple-700 border border-purple-200 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Admin</span>
                        @elseif($user->role == 'kepala_yayasan')
                            <span class="bg-blue-100 text-blue-700 border border-blue-200 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Yayasan</span>
                        @else
                            <span class="bg-green-100 text-green-700 border border-green-200 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider">Guru</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center flex justify-center gap-2">
                        <a href="{{ route('admin.user.edit', $user->id) }}" class="bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white w-8 h-8 rounded-lg flex items-center justify-center transition-colors">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>

                        @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="inline-block form-delete">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white w-8 h-8 rounded-lg flex items-center justify-center transition-colors btn-delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-delete');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                const form = this.closest('.form-delete');
                Swal.fire({
                    title: 'Hapus Akun?',
                    text: "Akun ini tidak akan bisa login lagi ke dalam sistem!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#9ca3af',
                    confirmButtonText: '<i class="fa-solid fa-trash"></i> Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush