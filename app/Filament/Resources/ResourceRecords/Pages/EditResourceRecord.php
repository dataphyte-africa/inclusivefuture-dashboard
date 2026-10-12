<?php

namespace App\Filament\Resources\ResourceRecords\Pages;

use App\Filament\Resources\ResourceRecords\ResourceRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResourceRecord extends EditRecord
{
    protected static string $resource = ResourceRecordResource::class;

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
