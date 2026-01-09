<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Penting untuk API

class User extends Authenticatable implements FilamentUser // Implementasi FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable; // Tambahkan HasApiTokens

    /**
     * Field yang boleh diisi
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // Pastikan role ditambahkan di sini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * LOGIC PROTEKSI: 
     * Menentukan siapa yang bisa masuk ke Dashboard Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Hanya user dengan role 'admin' yang bisa masuk panel admin
        // User dengan role 'member' akan ditolak (403)
        return in_array($this->role, ['admin', 'member']);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class);
    }

}