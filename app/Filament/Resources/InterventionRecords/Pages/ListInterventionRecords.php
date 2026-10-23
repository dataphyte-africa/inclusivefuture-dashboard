<?php

namespace App\Filament\Resources\InterventionRecords\Pages;

use App\Filament\Imports\InterventionRecordImporter;
use App\Filament\Resources\InterventionRecords\InterventionRecordResource;
use App\Models\InterventionRecord;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListInterventionRecords extends ListRecords
{
    protected static string $resource = InterventionRecordResource::class;

    public function getSubheading(): ?string
    {
        $count = InterventionRecord::query()->count();

        return number_format($count).' total intervention record'.($count === 1 ? '' : 's');
    }

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()->importer(InterventionRecordImporter::class)->chunkSize(200),
            CreateAction::make(),
        ];
    }
}
