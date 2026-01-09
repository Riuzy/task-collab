<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class AdminTaskController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $tasks = Task::with('user:id,name')->get();

        return response()->json([
            'status' => 'success',
            'data' => $tasks
        ]);
    }
}