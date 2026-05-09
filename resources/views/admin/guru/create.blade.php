@extends('layouts.app')

@section('title', 'Tambah Pegawai & Guru')
@section('page_title', 'Tambah Data Pegawai & Guru')

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

    <!-- Form tanpa enctype multipart karena tidak ada upload file -->
    <form action="{{ route('admin.guru.store') }}" method="POST" @submit="validateForm">
        @csrf

        <!-- INFORMASI AKUN -->
        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 text-blue-600"><i class="fa-solid fa-user-lock mr-2"></i> Informasi Akun (Sistem)</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Cth: Budi Santoso, S.Pd">
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Default <span class="text-red-500">*</span></label>
                <input type="text" name="password" value="password123" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-600 focus:ring-blue-500 focus:border-blue-500" readonly title="Password default sistem">
                <p class="text-[11px] text-gray-400 mt-1">Default: password123 (bisa diubah saat edit)</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
                <select name="jabatan" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled selected hidden>Pilih Jabatan</option>
                    <option value="Kepala Sekolah">Kepala Sekolah</option>
                    <option value="Guru Kelas">Guru Kelas</option>
                    <option value="Guru Mata Pelajaran">Guru Mata Pelajaran</option>
                    <option value="Guru BK">Guru BK</option>
                    <option value="Pegawai / Staff">Pegawai / Staff Administrasi</option>
                    <option value="Satpam / Security">Satpam / Security</option>
                </select>
            </div>
        </div>

        <!-- BIODATA LENGKAP -->
        <h4 class="font-bold text-gray-800 border-b pb-2 mb-4 text-blue-600 mt-6"><i class="fa-solid fa-address-book mr-2"></i> Biodata Lengkap</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Pegawai <span class="text-red-500">*</span></label>
                <select name="status_pegawai" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled selected hidden>Pilih Status</option>
                    <option value="Tetap">Pegawai Tetap</option>
                    <option value="Honorer">Honorer</option>
                    <option value="Kontrak">Kontrak</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled selected hidden>Pilih Jenis Kelamin</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            
            <!-- Tempat Lahir dengan API Datalist -->
            <div class="relative">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" list="cities" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ketik nama kota/kabupaten...">
                <datalist id="cities">
                    <template x-for="city in citiesList" :key="city.id">
                        <option :value="city.nama"></option>
                    </template>
                </datalist>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                <select name="agama" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled selected hidden>Pilih Agama</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen Protestan">Kristen Protestan</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                <input type="date" name="tanggal_bergabung" value="{{ old('tanggal_bergabung') ?? date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-[10px] text-gray-400 mt-1">Otomatis terisi tanggal hari ini jika dibiarkan.</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Handphone</label>
                <input type="number" name="no_hp" value="{{ old('no_hp') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Cth: 08123456789">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan Terakhir</label>
                <select name="pendidikan_terakhir" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="" disabled selected hidden>Pilih Pendidikan</option>
                    <option value="SMA/SMK">SMA / SMK Sederajat</option>
                    <option value="D1">Diploma 1 (D1)</option>
                    <option value="D3">Diploma 3 (D3)</option>
                    <option value="S1">Strata 1 (S1)</option>
                    <option value="S2">Strata 2 (S2)</option>
                    <option value="S3">Strata 3 (S3)</option>
                </select>
            </div>
            
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Masukkan nama jalan, desa/kelurahan, kecamatan...">{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-5 border-t border-gray-200">
            <a href="{{ route('admin.guru.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-100 transition">Batal</a>
            <button type="submit" :disabled="nikError" class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition shadow-md disabled:bg-blue-300 disabled:cursor-not-allowed">
                <i class="fa-solid fa-save mr-2"></i> Simpan Data
            </button>
        </div>
    </form>
</div>

<!-- Alpine JS Script untuk Logika Form -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('formData', () => ({
            nik: '{{ old('nik') }}', 
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
                        text: 'NIK harus terdiri dari 16 digit angka. Silakan periksa kembali!',
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