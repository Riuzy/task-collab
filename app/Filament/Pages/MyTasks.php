<?php

namespace App\Filament\Pages;

use App\Models\TaskProgress;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use App\Models\Task;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;

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

   public function addProgressAction(): Action
{
    return Action::make('addProgress')
        ->label('Detail & Update Progres')
        ->icon('heroicon-m-chat-bubble-left-right')
        ->color('info')
        ->size('sm')
        ->modalWidth('2xl')
        ->slideOver()
        // Ganti infolist dengan modalContent agar kita bisa kirim data manual ke view kustom
        ->modalContent(function (array $arguments) {
            $task = Task::with(['progress.user'])->find($arguments['taskId']);
            return view('filament.pages.task-detail-modal', [
                'task' => $task
            ]);
        })
        ->form([
            Textarea::make('content')
                ->label('Tambah Progres Baru')
                ->required()
                ->rows(3),
        ])
        ->action(function (array $data, array $arguments) {
            TaskProgress::create([
                'task_id' => $arguments['taskId'],
                'user_id' => Auth::id(),
                'content' => $data['content'],
            ]);

            Notification::make()
                ->title('Progres berhasil ditambahkan')
                ->success()
                ->send();
        });
}

    public function progress()
    {
        // Relasi Task ke TaskProgress (Satu tugas punya banyak catatan progres)
        return $this->hasMany(TaskProgress::class);
    }
}