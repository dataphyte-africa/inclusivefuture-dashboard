<?php

namespace App\Filament\Resources\InterventionRecords\Pages;

use App\Filament\Resources\InterventionRecords\InterventionRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInterventionRecord extends CreateRecord
{
    protected static string $resource = InterventionRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->canPublishRecords()) {
            $data['status'] = 'draft';
        }

        return $data;
    }
}
