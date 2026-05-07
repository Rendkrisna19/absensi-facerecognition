<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Admin (Akses Penuh Sistem)
        User::create([
            'name' => 'Administrator Sistem',
            'username' => 'admin_trijaya',
            'nik' => '1234567890123456', // NIK untuk Login Admin
            'password' => Hash::make('password123'), // Password default
            'role' => 'admin',
            'jabatan' => 'IT Support',
        ]);

        User::create([
            'name' => 'Darnah Purba, S.Pd.',
            'username' => 'darnah_purba',
            'nik' => '1111222233334444', 
            'password' => Hash::make('password123'),
            'role' => 'kepala_yayasan',
            'jabatan' => 'Kepala Yayasan',
        ]);

        User::create([
            'name' => 'Novita Yohana Maria Hasibuan',
            'username' => 'novita_yohana',
            'nik' => '5555666677778888', 
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'jabatan' => 'Guru Tetap',
        ]);
        
        // 4. Tambahan Akun Guru (Untuk simulasi data tabel)
        User::create([
            'name' => 'Ahmad Hidayat, S.Pd.',
            'username' => 'ahmad_hidayat',
            'nik' => '9999888877776666',
            'password' => Hash::make('password123'),
            'role' => 'guru',
            'jabatan' => 'Guru Honor',
        ]);
    }
}