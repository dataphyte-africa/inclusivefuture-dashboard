<?php

namespace App\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DraftSelectedInterventionRecords
{
    public static function make(): BulkAction
    {
        return BulkAction::make('moveToDraft')
            ->label('Move selected to draft')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading('Move selected intervention records to draft?')
            ->modalDescription('Selected published records will be removed from the public dashboard. They can be published again later.')
            ->modalSubmitActionLabel('Move to draft')
            ->visible(fn (): bool => auth()->user()?->canPublishRecords() ?? false)
            ->action(function (Collection $records): void {
                abort_unless(auth()->user()?->canPublishRecords(), 403);

                $recordsToDraft = $records->filter(fn ($record): bool => $record->status !== 'draft');

                DB::transaction(fn () => $recordsToDraft->each(fn ($record) => $record->update([
                    'status' => 'draft',
                    'published_at' => null,
                ])));

                $count = $recordsToDraft->count();
                $notification = Notification::make();

                if ($count === 0) {
                    $notification->title('All selected records are already drafts')->info()->send();

                    return;
                }

                $notification->title("Moved {$count} intervention record".($count === 1 ? '' : 's').' to draft')
                    ->success()
                    ->send();
            });
    }
}
