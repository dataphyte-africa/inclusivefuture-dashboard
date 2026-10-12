<?php

namespace App\Filament\Resources\IssueRecords\Pages;

use App\Filament\Resources\IssueRecords\IssueRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIssueRecord extends EditRecord
{
    protected static string $resource = IssueRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->visible(fn (): bool => auth()->user()?->isAdmin() ?? false),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! auth()->user()?->canPublishRecords()) {
            $data['status'] = $this->getRecord()->status;
        }

        return $data;
    }
}
