<?php

namespace App\Filament\Pages;

use App\Models\Task;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyTasks extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.pages.my-tasks';
    protected static ?string $navigationLabel = 'Tugas Saya';

    // Pastikan hanya role 'member' yang melihat menu ini
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->role === 'member';
    }

    // Mengambil data tugas milik member yang login
    public function getTasks()
    {
        return Auth::user()->tasks()->latest()->get();
    }
}