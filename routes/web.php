<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Inventaris CRUD - admin only 
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/inventaris/create', [InventarisController::class, 'create'])->name('inventaris.create');
    });

    // Inventaris - all authenticated users can view
    Route::get('/inventaris', [InventarisController::class, 'index'])->name('inventaris.index');
    Route::get('/inventaris/{id}', [InventarisController::class, 'show'])->name('inventaris.show');
    
    // QR Code Actions 
    Route::get('/inventaris/{id}/download-qr', [InventarisController::class, 'downloadQr'])->name('inventaris.download-qr');
    Route::get('/inventaris/{id}/print-qr', [InventarisController::class, 'printQr'])->name('inventaris.print-qr');
    Route::get('/inventaris/{id}/edit', [InventarisController::class, 'edit'])->name('inventaris.edit')->middleware('role:admin');

    // Inventaris store/update/delete - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/inventaris', [InventarisController::class, 'store'])->name('inventaris.store');
        Route::put('/inventaris/{id}', [InventarisController::class, 'update'])->name('inventaris.update');
        Route::delete('/inventaris/{id}', [InventarisController::class, 'destroy'])->name('inventaris.destroy');
    });

    // Peminjaman
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/{id}', [PeminjamanController::class, 'show'])->name('peminjaman.show');

    // Peminjaman actions - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::post('/peminjaman/{id}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
        Route::post('/peminjaman/{id}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
        Route::get('/peminjaman/{id}/return', [PeminjamanController::class, 'returnForm'])->name('peminjaman.return');
        Route::post('/peminjaman/{id}/process-return', [PeminjamanController::class, 'processReturn'])->name('peminjaman.process-return');
    });

    // Laporan - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    });
});

require __DIR__.'/auth.php';