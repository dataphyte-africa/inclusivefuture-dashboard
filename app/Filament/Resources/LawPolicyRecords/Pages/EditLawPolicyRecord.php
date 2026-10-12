<?php

namespace App\Filament\Resources\LawPolicyRecords\Pages;

use App\Filament\Resources\LawPolicyRecords\LawPolicyRecordResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLawPolicyRecord extends EditRecord
{
    protected static string $resource = LawPolicyRecordResource::class;

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
