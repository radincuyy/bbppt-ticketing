<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PriorityController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect home to login or dashboard
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Tickets
    Route::resource('tickets', TicketController::class);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
    Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
    Route::post('/tickets/{ticket}/request-approval', [TicketController::class, 'requestApproval'])->name('tickets.request-approval');

    // Comments
    Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])->name('comments.store');

    // Approvals (Manager TI only)
    Route::prefix('approvals')->name('approvals.')->middleware('permission:approvals.view')->group(function () {
        Route::get('/', [ApprovalController::class, 'index'])->name('index');
        Route::post('/{ticket}/approve', [ApprovalController::class, 'approve'])->name('approve');
        Route::post('/{ticket}/reject', [ApprovalController::class, 'reject'])->name('reject');
    });

    // Reports (UC-11)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // Admin Routes (Master Data Management)
    Route::prefix('admin')->name('admin.')->middleware('permission:master.categories.manage|master.users.manage')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('priorities', PriorityController::class);
        Route::resource('users', UserController::class);
    });
});
