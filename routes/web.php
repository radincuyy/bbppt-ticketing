<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\KomentarController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\PrioritasController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Arahkan halaman utama ke login atau dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Rute Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Logout (Hanya user login)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Rute Terproteksi (Wajib Login)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Tiket
    Route::resource('tickets', TiketController::class);
    
    // Daftar Tugas (Tiket yang ditugaskan ke user - untuk Helpdesk & Teknisi)
    Route::get('/tasks', [TiketController::class, 'tasks'])->name('tasks.index');
    
    // Menu khusus pada tiket
    Route::post('/tickets/{id}/assign', [TiketController::class, 'assign'])->name('tickets.assign'); // Penugasan teknisi
    Route::post('/tickets/{id}/close', [TiketController::class, 'close'])->name('tickets.close'); // Menutup tiket
    Route::post('/tickets/{id}/request-approval', [TiketController::class, 'requestApproval'])->name('tickets.request-approval'); // Ajukan persetujuan

    // Komentar Tiket
    Route::post('/tickets/{id}/komentar', [KomentarController::class, 'store'])->name('komentar.store');

    // Persetujuan (Approval) - Khusus Manager TI/Team Lead yang punya izin
    Route::prefix('approvals')->name('approvals.')->middleware('permission:approvals.view')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::post('/{id}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [ApprovalController::class, 'reject'])->name('reject');
    });

    // Laporan (Reports)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // Rute Admin (Manajemen Data Master & Pengguna)
    Route::prefix('admin')->name('admin.')->middleware('permission:master.categories.manage|master.users.manage')->group(function () {
        Route::resource('kategori', KategoriController::class);
        Route::resource('prioritas', PrioritasController::class);
        Route::resource('users', UserController::class); // Kelola Pengguna
    });
});
