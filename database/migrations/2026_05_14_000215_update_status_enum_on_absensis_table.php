<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menggunakan raw statement agar lebih aman untuk tipe ENUM di MySQL
        DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('Hadir', 'Terlambat', 'Alpa', 'Sakit', 'Izin', 'Cuti') NOT NULL DEFAULT 'Alpa'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke struktur awal jika di-rollback
        DB::statement("ALTER TABLE absensis MODIFY COLUMN status ENUM('Hadir', 'Terlambat', 'Alpa') NOT NULL DEFAULT 'Alpa'");
    }
};