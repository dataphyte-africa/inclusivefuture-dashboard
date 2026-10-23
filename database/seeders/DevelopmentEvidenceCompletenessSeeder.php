<?php

namespace Database\Seeders;

use App\Models\InterventionRecord;
use App\Models\IssueRecord;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use LogicException;

/**
 * Fills optional evidence metadata for local development and testing only.
 * All values are explicitly marked as development fixtures and are not sourced facts.
 */
class DevelopmentEvidenceCompletenessSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('DevelopmentEvidenceCompletenessSeeder may only run in local or testing environments.');
        }

        $this->fillSourceMetadata(IssueRecord::query(), 'issue');
        $this->fillSourceMetadata(InterventionRecord::query(), 'intervention');
        $this->fillSourceMetadata(ResourceRecord::query(), 'resource');
        $this->fillSourceMetadata(LawPolicyRecord::query(), 'law-policy');

        IssueRecord::query()->orderBy('id')->chunkById(200, function ($records): void {
            foreach ($records as $record) {
                $year = $this->yearFor($record->record_key);
                $this->fillMissing($record, [
                    'period' => (string) $year,
                    'population_group' => $this->pick($record->record_key, ['Women aged 15–49', 'Girls aged 10–17', 'Children aged 0–17', 'All residents']),
                    'disaggregation' => $this->pick($record->record_key.'-disaggregation', ['All', 'Urban/rural', 'Age group', 'Sex']),
                    'data_type' => 'Development fixture — administrative',
                ]);
            }
        });

        InterventionRecord::query()->orderBy('id')->chunkById(200, function ($records): void {
            foreach ($records as $record) {
                $year = $this->yearFor($record->record_key);
                $startYear = $this->extractYear($record->start_date) ?? max(2020, ($this->extractYear($record->end_date) ?? $year) - 1);
                $start = $record->start_date ?: sprintf('%d-%02d-01', $startYear, $this->monthFor($record->record_key));
                $endYear = max($startYear, min($startYear + 1 + (abs(crc32($record->record_key.'-duration')) % 2), 2026));
                $end = $record->end_date ?: sprintf('%d-12-31', $endYear);
                $this->fillMissing($record, [
                    'start_date' => $start,
                    'end_date' => $end,
                    'coverage_geography' => $record->state ?: 'Nigeria-wide',
                    'implementation_status' => $this->pick($record->record_key, ['Planned', 'Active', 'Completed', 'Paused']),
                    'description' => 'Development fixture describing a representative implementation record; validate against source material before external use.',
                ]);
            }
        });

        ResourceRecord::query()->orderBy('id')->chunkById(200, function ($records): void {
            foreach ($records as $record) {
                $year = $record->allocation_year ?: $this->yearFor($record->record_key);
                $stage = str_contains(strtolower($record->resource_category), 'release')
                    ? 'Release'
                    : $this->pick($record->record_key, ['Allocation', 'Commitment', 'Release']);

                $this->fillMissing($record, [
                    'programme' => 'Development fixture programme — '.($record->issue_area ?: 'cross-sector'),
                    'funding_stage' => $stage,
                    'allocation_year' => $year,
                    'period' => (string) $year,
                ]);
            }
        });

        LawPolicyRecord::query()->orderBy('id')->chunkById(200, function ($records): void {
            foreach ($records as $record) {
                $year = $this->yearFor($record->record_key);
                $this->fillMissing($record, [
                    'enactment_date' => sprintf('%d-%02d-%02d', $year, $this->monthFor($record->record_key), 15),
                ]);
            }
        });

        Cache::forget('eim:public-records:v2');
        Cache::forget('eim:public-records:v3');
    }

    private function fillSourceMetadata($query, string $layer): void
    {
        $query->orderBy('id')->chunkById(200, function ($records) use ($layer): void {
            foreach ($records as $record) {
                $key = rawurlencode((string) $record->record_key);
                $this->fillMissing($record, [
                    'source_name' => 'Development fixture source (not an external citation)',
                    'source_url' => "https://testing.com/eim-development/{$layer}/{$key}",
                    'source_reference' => 'DEV-FIXTURE-'.$record->record_key,
                ]);
            }
        });
    }

    /** @param array<string, mixed> $values */
    private function fillMissing(Model $record, array $values): void
    {
        $dirty = false;
        foreach ($values as $column => $value) {
            if ($this->isBlank($record->getAttribute($column)) && ! $this->isBlank($value)) {
                $record->setAttribute($column, $value);
                $dirty = true;
            }
        }

        if ($dirty) {
            $record->save();
        }
    }

    private function isBlank(mixed $value): bool
    {
        return $value === null || (is_string($value) && trim($value) === '');
    }

    private function yearFor(string $key): int
    {
        return 2020 + (abs(crc32($key)) % 6);
    }

    private function extractYear(?string $date): ?int
    {
        if (! preg_match('/\b(19|20)\d{2}\b/', (string) $date, $matches)) {
            return null;
        }

        return (int) $matches[0];
    }

    private function monthFor(string $key): int
    {
        return 1 + (abs(crc32($key)) % 12);
    }

    /** @param list<string> $options */
    private function pick(string $key, array $options): string
    {
        return $options[abs(crc32($key)) % count($options)];
    }
}
