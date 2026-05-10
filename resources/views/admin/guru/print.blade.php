<!DOCTYPE html>
<html>
<head>
    <title>Profil Pegawai - {{ $guru->name }}</title>
    <style>
        body { font-family: sans-serif; color: #333; line-height: 1.5; padding: 20px; }
        .header { text-align: center; border-bottom: 4px solid #1a2a40; padding-bottom: 15px; margin-bottom: 25px; }
        .header h1 { margin: 0; color: #1a2a40; font-size: 26px; letter-spacing: 2px; }
        .header p { margin: 5px 0 0; font-size: 14px; font-weight: bold; color: #666; }
        
        .profile-title { background-color: #1a2a40; color: white; padding: 8px 15px; font-weight: bold; font-size: 14px; margin-bottom: 10px; border-radius: 4px; }
        
        /* Layout Pembagi Kiri (Info) & Kanan (Foto) */
        .layout-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .layout-info { width: 75%; vertical-align: top; }
        .layout-photo { width: 25%; vertical-align: top; text-align: right; padding-left: 20px; }
        
        /* Bingkai Foto */
        .photo-frame { 
            width: 120px; 
            height: 160px; 
            border: 3px solid #1a2a40; 
            border-radius: 8px; 
            object-fit: cover; 
            display: inline-block;
        }
        .no-photo {
            width: 120px; 
            height: 160px; 
            border: 2px dashed #999; 
            border-radius: 8px;
            display: inline-block;
            text-align: center;
            line-height: 160px;
            color: #999;
            font-size: 12px;
            background-color: #f9f9f9;
        }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td { padding: 8px 0; font-size: 14px; }
        .data-table td.label { width: 35%; font-weight: bold; color: #555; }
        .data-table td.separator { width: 2%; text-align: center; font-weight: bold; }
        .data-table td.value { width: 63%; color: #000; }
        
        .border-bottom { border-bottom: 1px dotted #ccc; }
        .footer { margin-top: 50px; text-align: right; font-size: 13px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>SEKOLAH TRI JAYA</h1>
        <p>BIODATA LENGKAP PEGAWAI</p>
    </div>

    <table class="layout-table">
        <tr>
            <!-- Bagian Kiri: Informasi Akun -->
            <td class="layout-info">
                <div class="profile-title">INFORMASI PEGAWAI (SISTEM)</div>
                <table class="data-table">
                    <tr class="border-bottom">
                        <td class="label">Nomor Induk (NIK)</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $guru->nik }}</td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="label">Nama Lengkap & Gelar</td>
                        <td class="separator">:</td>
                        <td class="value"><strong>{{ $guru->name }}</strong></td>
                    </tr>
                    <tr class="border-bottom">
                        <td class="label">Jabatan</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $guru->jabatan }}</td>
                    </tr>
                    <tr class="border-bottom">
                        {{-- <td class="label">Status Pegawai</td> --}}
                        <td class="separator">:</td>
                        <td class="value">{{ $guru->guru?->status_pegawai ?? 'Belum Diatur' }}</td>
                    </tr>
                </table>
            </td>
            
            <!-- Bagian Kanan: Foto Profil -->
            <td class="layout-photo">
                @if($guru->foto_profil && file_exists(public_path('storage/' . $guru->foto_profil)))
                    <!-- Menggunakan public_path() agar DomPDF bisa memuat gambar secara lokal dengan cepat -->
                    <img src="{{ public_path('storage/' . $guru->foto_profil) }}" class="photo-frame" alt="Foto Profil">
                @else
                    <div class="no-photo">Tidak Ada Foto</div>
                @endif
            </td>
        </tr>
    </table>

    <div class="profile-title">DATA DIRI LENGKAP</div>
    <table class="data-table">
        <tr class="border-bottom">
            <td class="label">Tempat, Tanggal Lahir</td>
            <td class="separator">:</td>
            <td class="value">
                {{ $guru->guru?->tempat_lahir ?? '-' }}, 
                {{ $guru->guru?->tanggal_lahir ? \Carbon\Carbon::parse($guru->guru->tanggal_lahir)->translatedFormat('d F Y') : '-' }}
            </td>
        </tr>
        <tr class="border-bottom">
            <td class="label">Jenis Kelamin</td>
            <td class="separator">:</td>
            <td class="value">{{ ($guru->guru?->jenis_kelamin == 'L') ? 'Laki-laki' : (($guru->guru?->jenis_kelamin == 'P') ? 'Perempuan' : '-') }}</td>
        </tr>
        <tr class="border-bottom">
            <td class="label">Agama</td>
            <td class="separator">:</td>
            <td class="value">{{ $guru->guru?->agama ?? '-' }}</td>
        </tr>
        <tr class="border-bottom">
            <td class="label">Pendidikan Terakhir</td>
            <td class="separator">:</td>
            <td class="value">{{ $guru->guru?->pendidikan_terakhir ?? '-' }}</td>
        </tr>
        <tr class="border-bottom">
            <td class="label">Nomor Handphone</td>
            <td class="separator">:</td>
            <td class="value">{{ $guru->guru?->no_hp ?? '-' }}</td>
        </tr>
        <tr class="border-bottom">
            <td class="label">Tanggal Bergabung</td>
            <td class="separator">:</td>
            <td class="value">{{ $guru->guru?->tanggal_bergabung ? \Carbon\Carbon::parse($guru->guru->tanggal_bergabung)->translatedFormat('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Lengkap</td>
            <td class="separator">:</td>
            <td class="value">{{ $guru->guru?->alamat ?? '-' }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Dicetak oleh Sistem Informasi Sekolah Tri Jaya</p>
        <p>Tanggal: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
    </div>

</body>
</html>