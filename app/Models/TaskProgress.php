<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskProgress extends Model
{
    use HasFactory;

    // Supaya bisa input data secara massal
    protected $fillable = [
        'task_id',
        'user_id',
        'content',
    ];

    // Relasi ke Task (Satu progres punya satu task)
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Relasi ke User (Satu progres diinput oleh satu user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}