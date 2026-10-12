<?php

namespace App\Filament\Resources\ResourceRecords\Tables;

use App\Filament\Actions\PublishSelectedRecords;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResourceRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_key')->searchable(), TextColumn::make('issue_area')->searchable(),
                TextColumn::make('state')->searchable(), TextColumn::make('funder_mda')->searchable()->limit(30),
                TextColumn::make('resource_category')->searchable()->limit(28), TextColumn::make('amount')->numeric(),
                TextColumn::make('currency'), TextColumn::make('status')->badge(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('publish')->label('Publish')->color('success')->requiresConfirmation()
                    ->visible(fn ($record): bool => (auth()->user()?->canPublishRecords() ?? false) && $record->status === 'draft')
                    ->action(function ($record): void {
                        abort_unless(auth()->user()?->canPublishRecords(), 403);
                        $record->update(['status' => 'published', 'published_at' => now()]);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    PublishSelectedRecords::make(),
                    DeleteBulkAction::make()->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
