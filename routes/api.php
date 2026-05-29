<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - CyberProtect UMKM
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================

// Auth - tidak perlu token
Route::prefix('auth')->group(function () {
    // Admin login & register
    Route::post('/admin/login', [AuthController::class, 'loginAdmin']);
    Route::post('/admin/register', [AuthController::class, 'registerAdmin']);

    // Kasir login
    Route::post('/kasir/login', [AuthController::class, 'loginKasir']);
});

// ==================== PROTECTED ROUTES ====================

Route::middleware(['auth:sanctum'])->group(function () {

    // Logout (semua role)
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Cek jam operasional (frontend polling untuk auto-logout)
    Route::get('/auth/check-hours', [AuthController::class, 'checkOperationalHours']);

    // ==================== ADMIN ROUTES ====================
    Route::prefix('admin')->middleware(['role:admin'])->group(function () {

        // Profil admin
        Route::get('/profile', [Admin\ProfileController::class, 'show']);
        Route::post('/profile', [Admin\ProfileController::class, 'update']);
        Route::post('/profile/change-password', [Admin\ProfileController::class, 'changePassword']);

        // Transaksi & Fraud
        Route::get('/transactions', [Admin\TransactionController::class, 'index']);
        Route::get('/transactions/{transaction}', [Admin\TransactionController::class, 'show']);
        Route::post('/transactions/{transaction}/review', [Admin\TransactionController::class, 'review']);

        // Keamanan & Log Login
        Route::get('/security', [Admin\SecurityController::class, 'index']);
        Route::post('/security/force-logout/{kasir}', [Admin\SecurityController::class, 'forceLogout']);

        // Notifikasi
        Route::get('/notifications', [Admin\SecurityController::class, 'notifications']);
        Route::post('/notifications/mark-read', [Admin\SecurityController::class, 'markRead']);

        // Manajemen Karyawan
        Route::get('/karyawan', [Admin\KaryawanController::class, 'index']);
        Route::post('/karyawan', [Admin\KaryawanController::class, 'store']);
        Route::put('/karyawan/{kasir}', [Admin\KaryawanController::class, 'update']);
        Route::delete('/karyawan/{kasir}', [Admin\KaryawanController::class, 'destroy']);
        Route::post('/karyawan/{kasir}/reset-password', [Admin\KaryawanController::class, 'resetPassword']);

        // Rule Based System
        Route::get('/fraud-rules', [Admin\FraudRuleController::class, 'show']);
        Route::put('/fraud-rules', [Admin\FraudRuleController::class, 'update']);

        // Produk (admin manage produk)
        Route::get('/products', [Admin\ProductController::class, 'index']);
        Route::post('/products', [Admin\ProductController::class, 'store']);
        Route::put('/products/{product}', [Admin\ProductController::class, 'update']);
        Route::delete('/products/{product}', [Admin\ProductController::class, 'destroy']);
    });

    // ==================== KASIR ROUTES ====================
    Route::prefix('kasir')->middleware(['role:kasir', 'operational.hours'])->group(function () {

        // Dashboard kasir
        Route::get('/dashboard', [User\TransactionController::class, 'dashboard']);

        // POS - Transaksi
        Route::get('/transactions', [User\TransactionController::class, 'index']);
        Route::post('/transactions', [User\TransactionController::class, 'store']);
        Route::get('/transactions/{transaction}', [User\TransactionController::class, 'show']);

        // Produk (kasir lihat saja)
        Route::get('/products/search', [Admin\ProductController::class, 'search']);
        Route::get('/products/categories', [Admin\ProductController::class, 'categories']);

        // Profil kasir
        Route::get('/profile', [User\ProfileController::class, 'show']);
        Route::post('/profile', [User\ProfileController::class, 'update']);
        Route::post('/profile/change-password', [User\ProfileController::class, 'changePassword']);

        // Notifikasi kasir (untuk lihat status transaksi yang di-review admin)
        Route::get('/notifications', [User\ProfileController::class, 'getNotifications']);
        Route::post('/notifications/{id}/read', [User\ProfileController::class, 'markNotificationRead']);
    });

    // ==================== SHARED ROUTES (admin & kasir) ====================
    Route::prefix('shared')->middleware(['role:admin,kasir'])->group(function () {
        Route::get('/products/search', [Admin\ProductController::class, 'search']);
        Route::get('/products/categories', [Admin\ProductController::class, 'categories']);
    });
});
