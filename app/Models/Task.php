<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'user_id',
    ];

    // Relasi balik ke User
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function progress() // Nama fungsi ini harus 'progress' sesuai yang ada di infolist
    {
        return $this->hasMany(TaskProgress::class, 'task_id');
    }
}