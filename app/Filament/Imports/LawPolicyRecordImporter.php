<?php

namespace App\Filament\Imports;

use App\Models\LawPolicyRecord;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class LawPolicyRecordImporter extends Importer
{
    protected static ?string $model = LawPolicyRecord::class;
    protected static bool $shouldPreventFormulaInjection = true;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('record_key')->requiredMapping()->rules(['required', 'string', 'max:120']),
            ImportColumn::make('issue_area')->requiredMapping()->rules(['required', 'string', 'max:160']),
            ImportColumn::make('state')->rules(['nullable', 'string', 'max:100']),
            ImportColumn::make('geography_level')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('instrument_title')->requiredMapping()->rules(['required', 'string', 'max:255']),
            ImportColumn::make('instrument_category')->requiredMapping()->rules(['required', 'string', 'max:150']),
            ImportColumn::make('law_policy_family')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('jurisdiction')->requiredMapping()->rules(['required', 'string', 'max:100']),
            ImportColumn::make('status_label')->requiredMapping()->rules(['required', 'string', 'max:120']),
            ImportColumn::make('enactment_date')->rules(['nullable', 'string', 'max:50']),
            ImportColumn::make('source_name')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('source_url')->rules(['nullable', 'url', 'max:2048']),
            ImportColumn::make('source_reference')->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('notes')->rules(['nullable', 'string']),
        ];
    }

    public function resolveRecord(): ?LawPolicyRecord
    {
        return LawPolicyRecord::firstOrNew(['record_key' => $this->data['record_key']]);
    }

    public function beforeSave(): void
    {
        $this->record->status = 'draft';
        $this->record->published_at = null;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return number_format($import->successful_rows).' law / policy rows imported as drafts. '.number_format($import->getFailedRowsCount()).' rows failed validation.';
    }
}
