<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        // Mengambil tugas yang terelasi dengan user yang sedang login
        $tasks = auth()->user()->tasks()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'status' => 'success',
            'data'   => $tasks
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        // 1. Cari tugas spesifik ($id) yang HANYA dimiliki oleh user ini
        $task = auth()->user()->tasks()->find($id);

        // 2. Jika tugas tidak ditemukan di daftar milik user tersebut
        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan atau akses ditolak'], 404);
        }

        // 3. Validasi input
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        // 4. Update statusnya
        $task->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status tugas berhasil diperbarui',
            'task' => $task
        ]);
    }
}