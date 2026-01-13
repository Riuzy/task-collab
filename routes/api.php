<?php

use App\Http\Controllers\Api\AdminTaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

// Public Route
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    
    // --- FITUR MEMBER ---
    Route::get('/my-tasks', [TaskController::class, 'index']); // Daftar tugas member
    Route::get('/tasks/{id}', [TaskController::class, 'show']); // LIHAT DETAIL & HISTORI (Penting!)
    Route::post('/tasks/{id}/progress', [TaskController::class, 'storeProgress']); // Tambah laporan
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);

    // --- FITUR ADMIN (Gunakan Prefix /admin agar tidak bentrok) ---
    Route::prefix('admin')->group(function () {
        Route::get('/tasks', [AdminTaskController::class, 'index']); // Monitoring semua tugas
        Route::get('/users', [AdminTaskController::class, 'users']);
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});