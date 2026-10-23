<?php

namespace App\Filament\Resources\InterventionRecords\Tables;

use App\Filament\Actions\DraftSelectedInterventionRecords;
use App\Filament\Actions\PublishSelectedRecords;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InterventionRecordsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('record_key')->searchable(), TextColumn::make('name')->searchable()->limit(40),
                TextColumn::make('issue_area')->searchable(), TextColumn::make('state')->searchable(),
                TextColumn::make('intervention_type')->searchable()->limit(28),
                TextColumn::make('status')->badge(), TextColumn::make('updated_at')->dateTime()->sortable(),
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
                    DraftSelectedInterventionRecords::make(),
                    PublishSelectedRecords::make(),
                    DeleteBulkAction::make()->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
                ]),
            ]);
    }
}
