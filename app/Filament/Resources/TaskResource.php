<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Actions\ViewAction;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Detail Tugas')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('title')
                        ->label('Judul Tugas')
                        ->required()
                        ->maxLength(255),
                    
                    \Filament\Forms\Components\Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'in_progress' => 'In Progress',
                            'completed' => 'Completed',
                        ])
                        ->default('pending')
                        ->required(),

                    \Filament\Forms\Components\Textarea::make('description')
                        ->columnSpanFull(),

                    // Dropdown untuk memilih Member
                    \Filament\Forms\Components\Select::make('users')
                        ->label('Ditugaskan Kepada')
                        ->relationship(
                            name: 'users', 
                            titleAttribute: 'name',
                            modifyQueryUsing: fn (Builder $query) => $query->where('role', '!=', 'admin') 
                        )
                        ->searchable()
                        ->multiple()
                        ->preload()
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                ->label('Judul Tugas')
                ->searchable()
                ->sortable(),

                Tables\Columns\TextColumn::make('users.name')
                    ->label('Nama Member')
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'info',
                        'completed' => 'success',
                    })
                    ->label('Status Progres'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                ]),
            ])
            ->actions([
             ViewAction::make()
                ->label('Monitoring')
                ->icon('heroicon-o-presentation-chart-line')
                ->color('success')
                ->modalHeading('Monitoring & Update Status')
                ->form([
                    \Filament\Forms\Components\Section::make('Kontrol Status')
                        ->description('Ubah status pengerjaan tugas di sini.')
                        ->schema([
                            Select::make('status')
                                ->label('Status Tugas')
                                ->options([
                                    'pending' => 'Pending',
                                    'in_progress' => 'In Progress / Sedang Dikerjakan',
                                    'completed' => 'Selesai (Approved)',
                                ])
                                ->required(),
                        ])
                ])
                // Logika simpan hanya untuk status
                ->action(function (array $data, $record) {
                    $record->update([
                        'status' => $data['status']
                    ]);

                    \Filament\Notifications\Notification::make()
                        ->title('Status berhasil diperbarui')
                        ->success()
                        ->send();
                }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Tugas')
                    ->schema([
                        TextEntry::make('title')->label('Judul Tugas'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'in_progress' => 'info',
                                'completed' => 'success',
                            }),
                        TextEntry::make('description')
                            ->label('Deskripsi Lengkap')
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Histori Progres Member')
                    ->description('Riwayat laporan perkembangan dari tim')
                    ->schema([
                        // Mengambil data dari relasi 'progress' di Model Task
                        RepeatableEntry::make('progress')
                            ->label(false)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Oleh Member')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('created_at')
                                    ->label('Waktu Lapor')
                                    ->dateTime()
                                    ->color('gray'),
                                TextEntry::make('content')
                                    ->label('Catatan Progres')
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role === 'admin';
    }
}