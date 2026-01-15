<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaskProgress;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Cukup satu fungsi index agar tidak redeclare
    public function index()
    {
        // Eager load relasi progress agar tidak Null di Flutter
        $tasks = auth()->user()->tasks()
            ->with(['progress.user']) 
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'status' => 'success',
            'data'   => $tasks
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $task = auth()->user()->tasks()->find($id);

        if (!$task) {
            return response()->json(['message' => 'Tugas tidak ditemukan'], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $task->update(['status' => $request->status]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status berhasil diperbarui',
            'task' => $task
        ]);
    }
    public function storeProgress(Request $request, $id)
    {
        // 1. Validasi input
        $request->validate([
            'content' => 'required|string',
        ]);

        // 2. Cari Task berdasarkan ID
        $task = Task::findOrFail($id);

        // 3. Simpan data progres
        $progress = TaskProgress::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(), // Mengambil ID Member yang login
            'content' => $request->content,
        ]);

        // 4. Return respon sukses (Status Code 201)
        return response()->json([
            'message' => 'Progress berhasil ditambahkan',
            'data' => $progress->load('user') // Load relasi user untuk dikirim balik
        ], 201);
    }
}