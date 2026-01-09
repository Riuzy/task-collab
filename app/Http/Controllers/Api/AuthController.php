<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Handle Login dan Generate Token Enkripsi
     */
    public function login(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cari User berdasarkan Email
        $user = User::where('email', $request->email)->first();

        // 3. Cek Password (Hash check otomatis membandingkan teks asli dengan data terenkripsi)
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email atau Password salah',
            ], 401);
        }

        // 4. Hapus token lama agar hanya ada satu sesi aktif (Opsional)
        $user->tokens()->delete();

        // 5. Buat Token Baru (Sanctum mem-vaildasi ini di database)
        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login Berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'name' => $user->name,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Handle Logout (Hapus Token)
     */
    public function logout()
    {
        // Menghapus token yang sedang digunakan saat ini
        auth()->user()->currentAccessToken()->delete(); 

        return response()->json([
            'status' => 'success',
            'message' => 'Logout Berhasil, Token telah dihapus',
        ]);
    }
}