<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('role')->formatStateUsing(fn (string $state): string => match ($state) {
                    User::ROLE_ADMIN => 'Admin',
                    User::ROLE_EDITOR => 'Editor',
                    default => 'Data entry',
                })->badge(),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('resetPassword')
                    ->label('Reset password')
                    ->icon(Heroicon::OutlinedKey)
                    ->schema([
                        TextInput::make('password')->label('New password')->password()->revealable()->required()->minLength(8)->confirmed(),
                        TextInput::make('password_confirmation')->label('Confirm new password')->password()->revealable()->required()->dehydrated(false),
                    ])
                    ->requiresConfirmation()
                    ->action(fn (array $data, User $record): bool => $record->update(['password' => $data['password']]))
                    ->visible(fn (User $record): bool => auth()->user()?->isAdmin() && ! $record->is(auth()->user())),
            ]);
    }
}
