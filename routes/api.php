<?php

use App\Http\Controllers\Api\AdminTaskController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

// Pastikan baris ini ada
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-tasks', [TaskController::class, 'index']);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::get('/tasks', [AdminTaskController::class, 'index']);
    Route::get('/admin/users', [AdminTaskController::class, 'users']);
    Route::post('/logout', [AuthController::class, 'logout']);
});