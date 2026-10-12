<?php

namespace App\Filament\Imports;

use App\Models\IssueRecord;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class IssueRecordImporter extends Importer
{
    protected static ?string $model = IssueRecord::class;
    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('record_key')->requiredMapping()->rules(['required', 'string', 'max:120']),
            ImportColumn::make('issue_area')->requiredMapping()->rules(['required', 'string', 'max:160']),
            ImportColumn::make('indicator')->requiredMapping()->rules(['required', 'string', 'max:200']),
            ImportColumn::make('state')->requiredMapping()->rules(['required', 'string', 'max:100']),
            ImportColumn::make('geography_level')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('value')->rules(['nullable', 'numeric']),
            ImportColumn::make('unit')->rules(['nullable', 'string', 'max:120']),
            ImportColumn::make('population_group')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('disaggregation')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('period')->rules(['nullable', 'string', 'max:100']),
            ImportColumn::make('data_type')->rules(['nullable', 'string', 'max:100']),
            ImportColumn::make('source_name')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('source_url')->rules(['nullable', 'url', 'max:2048']),
            ImportColumn::make('source_reference')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('notes')->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?IssueRecord
    {
        return IssueRecord::firstOrNew(['record_key' => $this->data['record_key']]);
    }

    public function beforeSave(): void
    {
        $this->record->status = 'draft';
        $this->record->published_at = null;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return number_format($import->successful_rows).' issue rows imported as drafts. '.number_format($import->getFailedRowsCount()).' rows failed validation.';
    }
}
