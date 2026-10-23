<?php

namespace App\Filament\Resources\IssueRecords\Pages;

use App\Filament\Imports\IssueRecordImporter;
use App\Filament\Imports\IssueRecordRelationshipImporter;
use App\Filament\Resources\IssueRecords\IssueRecordResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListIssueRecords extends ListRecords
{
    protected static string $resource = IssueRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()->importer(IssueRecordImporter::class)->chunkSize(200),
            ImportAction::make('importRecordRelationships')
                ->label('Import record relationships')
                ->modalHeading('Import explicit issue record relationships')
                ->importer(IssueRecordRelationshipImporter::class)
                ->chunkSize(200),
            CreateAction::make(),
        ];
    }
}
