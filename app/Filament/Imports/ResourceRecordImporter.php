<?php

namespace App\Filament\Imports;

use App\Models\ResourceRecord;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class ResourceRecordImporter extends Importer
{
    protected static ?string $model = ResourceRecord::class;
    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('record_key')->requiredMapping()->rules(['required', 'string', 'max:120']),
            ImportColumn::make('issue_area')->requiredMapping()->rules(['required', 'string', 'max:160']),
            ImportColumn::make('state')->requiredMapping()->rules(['required', 'string', 'max:100']),
            ImportColumn::make('geography_level')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('programme')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('funder_mda')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('resource_category')->requiredMapping()->rules(['required', 'string', 'max:160']),
            ImportColumn::make('funding_stage')->rules(['nullable', 'string', 'max:100']),
            ImportColumn::make('amount')->requiredMapping()->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('currency')->requiredMapping()->rules(['required', 'string', 'size:3']),
            ImportColumn::make('allocation_year')->rules(['nullable', 'integer', 'between:1900,2100']),
            ImportColumn::make('period')->rules(['nullable', 'string', 'max:100']),
            ImportColumn::make('source_name')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('source_url')->rules(['nullable', 'url', 'max:2048']),
            ImportColumn::make('source_reference')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('notes')->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?ResourceRecord
    {
        return ResourceRecord::firstOrNew(['record_key' => $this->data['record_key']]);
    }

    public function beforeSave(): void
    {
        $this->record->status = 'draft';
        $this->record->published_at = null;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return number_format($import->successful_rows).' resource rows imported as drafts. '.number_format($import->getFailedRowsCount()).' rows failed validation.';
    }
}
