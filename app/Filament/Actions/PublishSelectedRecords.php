<?php

namespace App\Filament\Actions;

use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PublishSelectedRecords
{
    public static function make(): BulkAction
    {
        return BulkAction::make('publishSelected')
            ->label('Publish selected')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Publish selected draft records?')
            ->modalDescription('This makes the selected draft records visible in the public dashboard. Already-published records will be left unchanged.')
            ->modalSubmitActionLabel('Publish records')
            ->visible(fn (): bool => auth()->user()?->canPublishRecords() ?? false)
            ->action(function (Collection $records): void {
                abort_unless(auth()->user()?->canPublishRecords(), 403);

                $drafts = $records->filter(fn ($record): bool => $record->status === 'draft');
                $publishedAt = now();

                DB::transaction(fn () => $drafts->each(fn ($record) => $record->update([
                    'status' => 'published',
                    'published_at' => $publishedAt,
                ])));

                $count = $drafts->count();
                $notification = Notification::make();

                if ($count === 0) {
                    $notification->title('No selected drafts to publish')->info()->send();

                    return;
                }

                $notification->title("Published {$count} selected record".($count === 1 ? '' : 's'))
                    ->success()
                    ->send();
            });
    }
}
