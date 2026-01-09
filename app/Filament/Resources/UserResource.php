<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Informasi Pengguna')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    \Filament\Forms\Components\TextInput::make('password')
                        ->password()
                        // Password hanya wajib diisi saat membuat user baru
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state)) // Jangan simpan jika kosong saat edit
                        ->maxLength(255),
                    \Filament\Forms\Components\Select::make('role')
                        ->options([
                            'admin' => 'Admin',
                            'member' => 'Member',
                        ])
                        ->required()
                        ->default('member'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('email')->searchable(),
                \Filament\Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'member' => 'success',
                    }),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'member' => 'Member',
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

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        // Admin tidak bisa menghapus dirinya sendiri
        return Auth::id() !== $record->id;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Hanya muncul di sidebar jika user adalah admin
        return auth()->user()?->role === 'admin';
    }
}