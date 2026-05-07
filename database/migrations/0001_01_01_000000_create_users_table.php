<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            // Menggunakan username lebih cocok untuk sistem lokal/LAN daripada email
            $table->string('username')->unique(); 
            $table->string('password');
            
            // Penentuan Hak Akses (Role)
            $table->enum('role', ['admin', 'kepala_yayasan', 'guru'])->default('guru');
            
            // Identitas Pegawai (Bisa kosong jika dia Admin)
            $table->string('nik')->nullable()->unique();
            $table->string('jabatan')->nullable();
            
            $table->string('foto_profil')->nullable()->comment('Path foto profil pengguna');
            $table->text('face_descriptor')->nullable()->comment('Data biometrik dari face-api.js');
            
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel bawaan Laravel untuk reset password dan session
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};