<?php

namespace App\Filament\Resources\LawPolicyRecords\Pages;

use App\Filament\Resources\LawPolicyRecords\LawPolicyRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLawPolicyRecord extends CreateRecord
{
    protected static string $resource = LawPolicyRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->canPublishRecords()) {
            $data['status'] = 'draft';
        }

        return $data;
    }
}
