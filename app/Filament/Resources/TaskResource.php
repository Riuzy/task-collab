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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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