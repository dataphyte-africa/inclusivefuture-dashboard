<?php

namespace App\Filament\Imports;

use App\Models\InterventionRecord;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class InterventionRecordImporter extends Importer
{
    protected static ?string $model = InterventionRecord::class;
    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('record_key')->requiredMapping()->rules(['required', 'string', 'max:120']),
            ImportColumn::make('issue_area')->requiredMapping()->rules(['required', 'string', 'max:160']),
            ImportColumn::make('state')->requiredMapping()->rules(['required', 'string', 'max:100']),
            ImportColumn::make('geography_level')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('name')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('intervention_type')->requiredMapping()->rules(['required', 'string', 'max:150']),
            ImportColumn::make('implementer')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('target_population')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('start_date')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('end_date')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('coverage_geography')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('implementation_status')->rules(['nullable', 'string', 'max:100']),
            ImportColumn::make('description')->rules(['nullable', 'string']),
            ImportColumn::make('source_name')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('source_url')->rules(['nullable', 'url', 'max:2048']),
            ImportColumn::make('source_reference')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('notes')->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?InterventionRecord
    {
        return InterventionRecord::firstOrNew(['record_key' => $this->data['record_key']]);
    }

    public function beforeSave(): void
    {
        $this->record->status = 'draft';
        $this->record->published_at = null;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return number_format($import->successful_rows).' intervention rows imported as drafts. '.number_format($import->getFailedRowsCount()).' rows failed validation.';
    }
}
