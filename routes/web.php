<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//import controller admin
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\FaceRecordingController;
use App\Http\Controllers\Admin\PengaturanLanController;
use App\Http\Controllers\Admin\PengaturanAbsensiController;
use App\Http\Controllers\Admin\UserController;
//import role kepala-yayasan
use App\Http\Controllers\KepalaYayasan\DashboardYayasanController;
use App\Http\Controllers\KepalaYayasan\LaporanAbsensiController;

//impoert role guru
use App\Http\Controllers\Guru\AbsensiGuruController;


Route::redirect('/', '/login');

// 2. Route Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Group Routes: ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('admin.dashboard');
    Route::resource('guru', GuruController::class)->names('admin.guru');
    Route::get('/face-recording', [FaceRecordingController::class, 'index'])->name('admin.face.index');
    Route::get('/face-recording/{guru}/record', [FaceRecordingController::class, 'record'])->name('admin.face.record');
    Route::post('/face-recording/{guru}/store', [FaceRecordingController::class, 'store'])->name('admin.face.store');
    Route::resource('pengaturan-lan', PengaturanLanController::class)->names('admin.pengaturan-lan');
    Route::get('/pengaturan-absensi', [PengaturanAbsensiController::class, 'index'])->name('admin.pengaturan-absensi.index');
    Route::post('/pengaturan-absensi', [PengaturanAbsensiController::class, 'store'])->name('admin.pengaturan-absensi.store');
    Route::resource('user', UserController::class, ['as' => 'admin']);
    Route::get('/admin/guru/export/excel', [\App\Http\Controllers\Admin\GuruController::class, 'exportExcel'])->name('admin.guru.export.excel');
    Route::get('/admin/guru/export/pdf', [\App\Http\Controllers\Admin\GuruController::class, 'exportPdf'])->name('admin.guru.export.pdf');
    Route::get('/admin/guru/{guru}/print', [\App\Http\Controllers\Admin\GuruController::class, 'print'])->name('admin.guru.print');
});

// 4. Group Routes: KEPALA YAYASAN
Route::middleware(['auth', 'role:kepala_yayasan'])->prefix('yayasan')->group(function () {
    Route::get('/dashboard', [DashboardYayasanController::class, 'index'])->name('yayasan.dashboard');
    Route::get('/laporan-kehadiran', [LaporanAbsensiController::class, 'index'])->name('yayasan.laporan.index');
});

// 5. Group Routes: GURU
Route::middleware(['auth', 'role:guru'])->prefix('guru')->group(function () {
    Route::get('/dashboard', [AbsensiGuruController::class, 'dashboard'])->name('guru.dashboard');
    Route::get('/riwayat', [AbsensiGuruController::class, 'riwayat'])->name('guru.riwayat');
    Route::get('/denda', [AbsensiGuruController::class, 'denda'])->name('guru.denda');
    Route::get('/pengaturan', [AbsensiGuruController::class, 'pengaturan'])->name('guru.pengaturan');
    Route::post('/pengaturan/update-profil', [AbsensiGuruController::class, 'updateProfil'])->name('guru.pengaturan.update');
    Route::post('/scan-absensi/store', [App\Http\Controllers\Guru\AbsensiGuruController::class, 'storeAbsensi'])->name('guru.scan.store');
    Route::get('/scan-absensi', [AbsensiGuruController::class, 'scan'])->name('guru.scan');
});
