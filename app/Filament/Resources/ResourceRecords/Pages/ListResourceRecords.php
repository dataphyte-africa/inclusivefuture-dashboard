<?php

namespace App\Filament\Resources\ResourceRecords\Pages;

use App\Filament\Imports\ResourceRecordImporter;
use App\Filament\Resources\ResourceRecords\ResourceRecordResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListResourceRecords extends ListRecords
{
    protected static string $resource = ResourceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()->importer(ResourceRecordImporter::class)->chunkSize(200),
            CreateAction::make(),
        ];
    }
}
