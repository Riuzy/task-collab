<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function users()
{
    // 1. Cek Role (Hanya Admin yang boleh tembus)
    if (auth()->user()->role !== 'admin') {
        return response()->json(['message' => 'Akses Ditolak. Hanya Admin yang diizinkan.'], 403);
    }

    // 2. Ambil Data User (Sembunyikan password & token sensitif)
    $users = \App\Models\User::select('id', 'name', 'email', 'role', 'created_at')->get();

    return response()->json([
        'status' => 'success',
        'total_users' => $users->count(),
        'data' => $users
    ]);
}
}