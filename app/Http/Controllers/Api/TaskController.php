<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
}