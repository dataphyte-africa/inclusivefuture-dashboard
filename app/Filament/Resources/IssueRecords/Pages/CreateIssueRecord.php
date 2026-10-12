<?php

namespace App\Filament\Resources\IssueRecords\Pages;

use App\Filament\Resources\IssueRecords\IssueRecordResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIssueRecord extends CreateRecord
{
    protected static string $resource = IssueRecordResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->canPublishRecords()) {
            $data['status'] = 'draft';
        }

        return $data;
    }
}
