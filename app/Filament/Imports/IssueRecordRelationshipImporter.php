<?php

namespace App\Filament\Imports;

use App\Models\InterventionRecord;
use App\Models\IssueInterventionLink;
use App\Models\IssueLawPolicyLink;
use App\Models\IssueRecord;
use App\Models\IssueResourceLink;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class IssueRecordRelationshipImporter extends Importer
{
    protected static ?string $model = IssueInterventionLink::class;

    protected static bool $shouldPreventFormulaInjection = true;

    protected ?int $resolvedIssueRecordId = null;

    protected ?int $resolvedRelatedRecordId = null;

    /** @var array{model: class-string<Model>, link: class-string<Model>, foreign_key: string, table: string}|null */
    protected ?array $resolvedRelationship = null;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('issue_record_key')->label('Issue record key')->requiredMapping()->rules(['required', 'string', 'max:120', 'exists:issue_records,record_key']),
            ImportColumn::make('related_record_type')->label('Related record type')->requiredMapping()->rules(['required', Rule::in(['intervention', 'resource', 'law'])]),
            ImportColumn::make('related_record_key')->label('Related record key')->requiredMapping()->rules(['required', 'string', 'max:120']),
            ImportColumn::make('relationship_type')->label('Relationship type')->requiredMapping()->rules(['required', 'string', 'max:60']),
            ImportColumn::make('source_reference')->label('Relationship source / reference')->requiredMapping()->rules(['required', 'string', 'max:2048']),
        ];
    }

    public function resolveRecord(): ?Model
    {
        $this->resolvedIssueRecordId = null;
        $this->resolvedRelatedRecordId = null;
        $this->resolvedRelationship = $this->relationshipConfig($this->data['related_record_type'] ?? null);

        if (! $this->resolvedRelationship) {
            return new IssueInterventionLink;
        }

        $issueKey = $this->data['issue_record_key'] ?? null;
        $relatedKey = $this->data['related_record_key'] ?? null;

        if (blank($issueKey) || blank($relatedKey)) {
            $linkModel = $this->resolvedRelationship['link'];

            return new $linkModel;
        }

        $this->resolvedIssueRecordId = IssueRecord::query()->where('record_key', $issueKey)->value('id');
        $relatedModel = $this->resolvedRelationship['model'];
        $this->resolvedRelatedRecordId = $relatedModel::query()->where('record_key', $relatedKey)->value('id');

        if (! $this->resolvedIssueRecordId || ! $this->resolvedRelatedRecordId) {
            $linkModel = $this->resolvedRelationship['link'];

            return new $linkModel;
        }

        $linkModel = $this->resolvedRelationship['link'];

        return $linkModel::query()->firstOrNew([
            'issue_record_id' => $this->resolvedIssueRecordId,
            $this->resolvedRelationship['foreign_key'] => $this->resolvedRelatedRecordId,
        ]);
    }

    public function validateData(): void
    {
        parent::validateData();

        $relationship = $this->relationshipConfig($this->data['related_record_type'] ?? null);

        if (! $relationship) {
            return;
        }

        Validator::validate(
            ['related_record_key' => $this->data['related_record_key'] ?? null],
            ['related_record_key' => ['required', Rule::exists($relationship['table'], 'record_key')]],
        );
    }

    public function fillRecord(): void
    {
        $this->record->forceFill([
            'issue_record_id' => $this->resolvedIssueRecordId,
            $this->resolvedRelationship['foreign_key'] => $this->resolvedRelatedRecordId,
            'relationship_type' => $this->data['relationship_type'],
            'source_reference' => $this->data['source_reference'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return number_format($import->successful_rows).' relationship rows imported or updated. '.number_format($import->getFailedRowsCount()).' rows failed validation.';
    }

    /**
     * @return array{model: class-string<Model>, link: class-string<Model>, foreign_key: string, table: string}|null
     */
    private function relationshipConfig(?string $type): ?array
    {
        return match ($type) {
            'intervention' => ['model' => InterventionRecord::class, 'link' => IssueInterventionLink::class, 'foreign_key' => 'intervention_record_id', 'table' => 'intervention_records'],
            'resource' => ['model' => ResourceRecord::class, 'link' => IssueResourceLink::class, 'foreign_key' => 'resource_record_id', 'table' => 'resource_records'],
            'law' => ['model' => LawPolicyRecord::class, 'link' => IssueLawPolicyLink::class, 'foreign_key' => 'law_policy_record_id', 'table' => 'law_policy_records'],
            default => null,
        };
    }
}
