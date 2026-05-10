<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    <table>
    <thead>
        <tr>
            <th colspan="10" style="text-align: center; font-size: 16px; font-weight: bold; background-color: #1a2a40; color: #ffffff;">DATA PEGAWAI DAN GURU - SEKOLAH TRI JAYA</th>
        </tr>
        <tr>
            <th colspan="10" style="text-align: center; background-color: #1a2a40; color: #ffffff;">Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</th>
        </tr>
        <tr>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">No</th>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">NIK</th>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">Nama Lengkap</th>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">Jabatan</th>
            {{-- <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">Status Pegawai</th> --}}
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">Jenis Kelamin</th>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">TTL</th>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">Pendidikan</th>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">No. HP</th>
            <th style="background-color: #1a2a40; color: #ffffff; font-weight: bold; border: 1px solid #000000; text-align: center;">Alamat</th>
        </tr>
    </thead>
    <tbody>
        @foreach($gurus as $index => $item)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ $item->nik }}</td>
            <td style="border: 1px solid #000000;">{{ $item->name }}</td>
            <td style="border: 1px solid #000000;">{{ $item->jabatan }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->status_pegawai ?? '-' }}</td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $item->guru?->jenis_kelamin == 'L' ? 'Laki-laki' : ($item->guru?->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->tempat_lahir ?? '-' }}, {{ $item->guru?->tanggal_lahir ? \Carbon\Carbon::parse($item->guru->tanggal_lahir)->format('d/m/Y') : '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->pendidikan_terakhir ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->no_hp ?? '-' }}</td>
            <td style="border: 1px solid #000000;">{{ $item->guru?->alamat ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
</body>
</html>