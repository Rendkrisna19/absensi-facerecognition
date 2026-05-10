<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

//import controller admin
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\FaceRecordingController;
use App\Http\Controllers\Admin\PengaturanLanController;
use App\Http\Controllers\Admin\PengaturanAbsensiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LiburSemesterController;
use App\Http\Controllers\Admin\DashboardController;
//import role kepala-yayasan
use App\Http\Controllers\KepalaYayasan\DashboardYayasanController;
use App\Http\Controllers\KepalaYayasan\LaporanAbsensiController;
use App\Http\Controllers\KepalaYayasan\PotonganController;

//impoert role guru
use App\Http\Controllers\Guru\AbsensiGuruController;




Route::redirect('/', '/login');

// 2. Route Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/profil-saya', [ProfileController::class, 'index'])->name('profile.index');
Route::put('/profil-saya', [ProfileController::class, 'update'])->name('profile.update');

// 3. Group Routes: ADMIN
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('guru', GuruController::class)->names('admin.guru');
    Route::get('/face-recording', [FaceRecordingController::class, 'index'])->name('admin.face.index');
    Route::get('/face-recording/{guru}/record', [FaceRecordingController::class, 'record'])->name('admin.face.record');
    Route::post('/face-recording/{guru}/store', [FaceRecordingController::class, 'store'])->name('admin.face.store');
    Route::resource('pengaturan-lan', PengaturanLanController::class)->names('admin.pengaturan-lan');
    Route::post('/pengaturan-lan/toggle/{id}', [PengaturanLanController::class, 'toggleStatus'])->name('admin.pengaturan-lan.toggle');
    Route::get('/pengaturan-absensi', [PengaturanAbsensiController::class, 'index'])->name('admin.pengaturan-absensi.index');
    Route::post('/pengaturan-absensi', [PengaturanAbsensiController::class, 'store'])->name('admin.pengaturan-absensi.store');
    Route::resource('user', UserController::class, ['as' => 'admin']);
    Route::get('/admin/guru/export/excel', [GuruController::class, 'exportExcel'])->name('admin.guru.export.excel');
    Route::get('/admin/guru/export/pdf', [GuruController::class, 'exportPdf'])->name('admin.guru.export.pdf');
    Route::get('/admin/guru/{guru}/print', [GuruController::class, 'print'])->name('admin.guru.print');
    Route::get('/user', [UserController::class, 'index'])->name('admin.user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('admin.user.create');
    Route::post('/user', [UserController::class, 'store'])->name('admin.user.store');
    Route::get('/user/{user}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
    Route::put('/user/{user}', [UserController::class, 'update'])->name('admin.user.update');
    Route::delete('/user/{user}', [UserController::class, 'destroy'])->name('admin.user.destroy');
    Route::resource('libur-semester', LiburSemesterController::class, ['as' => 'admin']);
    Route::get('/libur-semester', [LiburSemesterController::class, 'index'])->name('admin.libur-semester.index');
    Route::post('/libur-semester', [LiburSemesterController::class, 'store'])->name('admin.libur.store');
    Route::put('/libur-semester/{libur}', [LiburSemesterController::class, 'update'])->name('admin.libur.update');
    Route::delete('/libur-semester/{libur}', [LiburSemesterController::class, 'destroy'])->name('admin.libur.destroy');
});

// 4. Group Routes: KEPALA YAYASAN
Route::middleware(['auth', 'role:kepala_yayasan'])->prefix('yayasan')->group(function () {
    Route::get('/dashboard', [DashboardYayasanController::class, 'index'])->name('yayasan.dashboard');
    Route::get('/laporan-kehadiran', [LaporanAbsensiController::class, 'index'])->name('yayasan.laporan.index');
    Route::get('/yayasan/potongan', [PotonganController::class, 'index'])->name('yayasan.potongan.index');
    Route::get('/yayasan/potongan/pdf', [PotonganController::class, 'exportPdf'])->name('yayasan.potongan.pdf');
    Route::get('/yayasan/potongan/excel', [PotonganController::class, 'exportExcel'])->name('yayasan.potongan.excel');
    Route::get('/yayasan/laporan/pdf', [LaporanAbsensiController::class, 'exportPdf'])->name('yayasan.laporan.pdf');
    Route::get('/yayasan/laporan/excel', [LaporanAbsensiController::class, 'exportExcel'])->name('yayasan.laporan.excel');
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
