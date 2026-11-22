<?php

use App\Http\Controllers\Admin\PengajuanController;
use App\Http\Controllers\Admin\PerusahaanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Mitra\PelamarController;
use App\Http\Controllers\Siswa\PklController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/verifikasi/{kode}', [VerifikasiController::class, 'show'])->name('verifikasi');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:8,1');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', fn () => redirect()->route(match (auth()->user()->role) {
        'admin_hubin' => 'admin.pengajuan.index',
        'perusahaan'  => 'mitra.pelamar',
        default       => 'siswa.katalog',
    }))->name('dashboard');

    // ---- Siswa
    Route::middleware('role:siswa')->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/katalog', [PklController::class, 'katalog'])->name('katalog');
        Route::get('/katalog/{perusahaan}/ajukan', [PklController::class, 'create'])->name('ajukan');
        Route::post('/katalog/{perusahaan}/ajukan', [PklController::class, 'store'])->name('ajukan.store');
        Route::get('/pengajuan', [PklController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [PklController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/balasan', [PklController::class, 'unggahBalasan'])->name('pengajuan.balasan');
        Route::get('/pengajuan/{pengajuan}/surat', [PklController::class, 'surat'])->name('pengajuan.surat');
    });

    // ---- Admin Hubin
    Route::middleware('role:admin_hubin')->prefix('hubin')->name('admin.')->group(function () {
        Route::patch('/perusahaan/{perusahaan}/kuota', [PerusahaanController::class, 'kuota'])->name('perusahaan.kuota');
        Route::resource('perusahaan', PerusahaanController::class)->except('show');

        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
        Route::post('/pengajuan/{pengajuan}/setujui', [PengajuanController::class, 'setujui'])->name('pengajuan.setujui');
        Route::post('/pengajuan/{pengajuan}/tolak', [PengajuanController::class, 'tolak'])->name('pengajuan.tolak');
        Route::post('/pengajuan/{pengajuan}/guru', [PengajuanController::class, 'guru'])->name('pengajuan.guru');
        Route::get('/pengajuan/{pengajuan}/surat', [PengajuanController::class, 'surat'])->name('pengajuan.surat');
    });

    // ---- Perusahaan (mitra)
    Route::middleware('role:perusahaan')->prefix('mitra')->name('mitra.')->group(function () {
        Route::get('/pelamar', [PelamarController::class, 'index'])->name('pelamar');
        Route::post('/pelamar/{pengajuan}/terima', [PelamarController::class, 'terima'])->name('terima');
        Route::post('/pelamar/{pengajuan}/tolak', [PelamarController::class, 'tolak'])->name('tolak');
    });
});
