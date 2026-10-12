<?php

namespace App\Filament\Resources\LawPolicyRecords\Pages;

use App\Filament\Imports\LawPolicyRecordImporter;
use App\Filament\Resources\LawPolicyRecords\LawPolicyRecordResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListLawPolicyRecords extends ListRecords
{
    protected static string $resource = LawPolicyRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()->importer(LawPolicyRecordImporter::class)->chunkSize(200),
            CreateAction::make(),
        ];
    }
}
