<?php

namespace App\Filament\Resources\InterventionRecords\Pages;

use App\Filament\Resources\InterventionRecords\InterventionRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInterventionRecord extends EditRecord
{
    protected static string $resource = InterventionRecordResource::class;

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
