<?php

namespace App\Filament\Resources\ResourceRecords\Pages;

use App\Filament\Resources\ResourceRecords\ResourceRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateResourceRecord extends CreateRecord
{
    protected static string $resource = ResourceRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->canPublishRecords()) {
            $data['status'] = 'draft';
        }

        return $data;
    }
}
