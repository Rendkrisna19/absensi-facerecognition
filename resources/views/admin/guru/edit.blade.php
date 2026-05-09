@extends('layouts.app')

@section('title', 'Edit Pegawai & Guru')
@section('page_title', 'Edit Data Pegawai & Guru')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8" x-data="formData()">
    
    <!-- Tampilkan Error Validasi Global dari Laravel -->
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg mb-6 text-sm flex items-start gap-3">
            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.guru.update', $guru->id) }}" method="POST" @submit="validateForm">
        @csrf
        @method('PUT')

        <!-- INFORMASI AKUN -->
        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 text-blue-600"><i class="fa-solid fa-user-lock mr-2"></i> Informasi Akun (Sistem)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $guru->name ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Cth: Budi Santoso, S.Pd">
            </div>
            
            <!-- Input NIK dengan Validasi Alpine -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK (Digunakan u/ Login) <span class="text-red-500">*</span></label>
                <input type="number" name="nik" x-model="nik" required 
                       :class="{'border-red-500 bg-red-50 focus:ring-red-500 focus:border-red-500': nikError, 'border-gray-300 focus:ring-blue-500 focus:border-blue-500': !nikError}"
                       class="w-full px-4 py-2 border rounded-lg transition-colors" 
                       placeholder="Masukkan 16 digit NIK">
                <p x-show="nikError" x-cloak class="text-red-500 text-xs mt-1 font-medium">
                    <i class="fa-solid fa-triangle-exclamation"></i> NIK harus persis 16 digit (Saat ini: <span x-text="nik.length"></span> digit)
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ubah Password</label>
                <input type="text" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400" placeholder="Kosongkan jika tidak ingin mengubah password">
                <span class="text-[11px] text-gray-500 mt-1 block"><i class="fa-solid fa-circle-info mr-1"></i> Hanya diisi jika user meminta reset password.</span>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled hidden>Pilih Jabatan</option>
                    <option value="Kepala Sekolah" {{ old('jabatan', $guru->jabatan) == 'Kepala Sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                    <option value="Guru Kelas" {{ old('jabatan', $guru->jabatan) == 'Guru Kelas' ? 'selected' : '' }}>Guru Kelas</option>
                    <option value="Guru Mata Pelajaran" {{ old('jabatan', $guru->jabatan) == 'Guru Mata Pelajaran' ? 'selected' : '' }}>Guru Mata Pelajaran</option>
                    <option value="Guru BK" {{ old('jabatan', $guru->jabatan) == 'Guru BK' ? 'selected' : '' }}>Guru BK</option>
                    <option value="Pegawai / Staff" {{ old('jabatan', $guru->jabatan) == 'Pegawai / Staff' ? 'selected' : '' }}>Pegawai / Staff Administrasi</option>
                    <option value="Satpam / Security" {{ old('jabatan', $guru->jabatan) == 'Satpam / Security' ? 'selected' : '' }}>Satpam / Security</option>
                </select>
            </div>
        </div>

        <!-- BIODATA LENGKAP -->
        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 text-blue-600 mt-6"><i class="fa-solid fa-address-book mr-2"></i> Biodata Lengkap</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pegawai <span class="text-red-500">*</span></label>
                <select name="status_pegawai" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled hidden>Pilih Status</option>
                    <option value="Tetap" {{ old('status_pegawai', $guru->guru->status_pegawai ?? '') == 'Tetap' ? 'selected' : '' }}>Pegawai Tetap</option>
                    <option value="Honorer" {{ old('status_pegawai', $guru->guru->status_pegawai ?? '') == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                    <option value="Kontrak" {{ old('status_pegawai', $guru->guru->status_pegawai ?? '') == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled hidden>Pilih Jenis Kelamin</option>
                    <option value="L" {{ old('jenis_kelamin', $guru->guru->jenis_kelamin ?? '') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="P" {{ old('jenis_kelamin', $guru->guru->jenis_kelamin ?? '') == 'P' ? 'selected' : '' }}>Perempuan</option>
                </select>
            </div>
            
            <!-- Tempat Lahir dengan API Datalist -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir <span class="text-red-500">*</span></label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $guru->guru->tempat_lahir ?? '') }}" required list="cities" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik nama kota/kabupaten...">
                <datalist id="cities">
                    <template x-for="city in citiesList" :key="city.id">
                        <option :value="city.nama"></option>
                    </template>
                </datalist>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $guru->guru->tanggal_lahir ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agama <span class="text-red-500">*</span></label>
                <select name="agama" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled hidden>Pilih Agama</option>
                    <option value="Islam" {{ old('agama', $guru->guru->agama ?? '') == 'Islam' ? 'selected' : '' }}>Islam</option>
                    <option value="Kristen Protestan" {{ old('agama', $guru->guru->agama ?? '') == 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                    <option value="Katolik" {{ old('agama', $guru->guru->agama ?? '') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                    <option value="Hindu" {{ old('agama', $guru->guru->agama ?? '') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                    <option value="Buddha" {{ old('agama', $guru->guru->agama ?? '') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                    <option value="Konghucu" {{ old('agama', $guru->guru->agama ?? '') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_bergabung" value="{{ old('tanggal_bergabung', $guru->guru->tanggal_bergabung ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Handphone <span class="text-red-500">*</span></label>
                <input type="number" name="no_hp" value="{{ old('no_hp', $guru->guru->no_hp ?? '') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Cth: 08123456789">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir <span class="text-red-500">*</span></label>
                <select name="pendidikan_terakhir" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled hidden>Pilih Pendidikan</option>
                    <option value="SMA/SMK" {{ old('pendidikan_terakhir', $guru->guru->pendidikan_terakhir ?? '') == 'SMA/SMK' ? 'selected' : '' }}>SMA / SMK Sederajat</option>
                    <option value="D1" {{ old('pendidikan_terakhir', $guru->guru->pendidikan_terakhir ?? '') == 'D1' ? 'selected' : '' }}>Diploma 1 (D1)</option>
                    <option value="D3" {{ old('pendidikan_terakhir', $guru->guru->pendidikan_terakhir ?? '') == 'D3' ? 'selected' : '' }}>Diploma 3 (D3)</option>
                    <option value="S1" {{ old('pendidikan_terakhir', $guru->guru->pendidikan_terakhir ?? '') == 'S1' ? 'selected' : '' }}>Strata 1 (S1)</option>
                    <option value="S2" {{ old('pendidikan_terakhir', $guru->guru->pendidikan_terakhir ?? '') == 'S2' ? 'selected' : '' }}>Strata 2 (S2)</option>
                    <option value="S3" {{ old('pendidikan_terakhir', $guru->guru->pendidikan_terakhir ?? '') == 'S3' ? 'selected' : '' }}>Strata 3 (S3)</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama jalan, desa/kelurahan, kecamatan...">{{ old('alamat', $guru->guru->alamat ?? '') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
            <a href="{{ route('admin.guru.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition">Batal</a>
            <button type="submit" :disabled="nikError" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-md disabled:bg-blue-300 disabled:cursor-not-allowed">
                <i class="fa-solid fa-check-double mr-2"></i> Perbarui Data
            </button>
        </div>
    </form>
</div>

<!-- Alpine JS Script untuk Logika Form -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formData', () => ({
            // Inisialisasi state dengan data lama/existing dari server
            nik: '{{ old('nik', $guru->nik ?? '') }}', 
            citiesList: [],
            
            // Computed property untuk cek error NIK
            get nikError() {
                if (this.nik.length === 0) return false;
                return this.nik.length !== 16;
            },

            init() {
                this.fetchCities();
            },

            // Fungsi Mencegah Submit jika NIK salah
            validateForm(e) {
                if (this.nikError) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'NIK harus terdiri dari persis 16 digit angka. Silakan periksa kembali!',
                        confirmButtonColor: '#3b82f6'
                    });
                }
            },

            // Fungsi Tarik API Kabupaten/Kota Indonesia
            async fetchCities() {
                try {
                    const response = await fetch('https://ibnux.github.io/data-indonesia/kabupaten.json');
                    const data = await response.json();
                    
                    this.citiesList = data.map(city => {
                        let cleanName = city.nama.replace(/^(KAB\.|KOTA)\s+/i, '');
                        cleanName = cleanName.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                        return { id: city.id, nama: cleanName };
                    });
                } catch (error) {
                    console.error("Gagal menarik data kota:", error);
                }
            }
        }));
    });
</script>

<style>
    /* CSS tambahan agar Alpine x-cloak bekerja sempurna sebelum file JS dimuat */
    [x-cloak] { display: none !important; }
</style>
@endsection