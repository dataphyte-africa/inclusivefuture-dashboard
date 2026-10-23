<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class ImportEimRealData extends Command
{
    protected $signature = 'eim:import-real-data {--dry-run : Validate and report without writing} {--path= : CSV directory relative to the project root}';

    protected $description = 'Validate and import the EIM 2025 CSV package into the local development database as drafts.';

    private array $recordTables = [
        'issue' => 'issue_records',
        'intervention' => 'intervention_records',
        'resource' => 'resource_records',
        'law_policy' => 'law_policy_records',
        'law' => 'law_policy_records',
    ];

    private array $recordKeys = [];

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('This import is restricted to the local environment.');

            return self::FAILURE;
        }

        if (DB::connection()->getDatabaseName() !== 'if_dashboard') {
            $this->error('Refusing to import: the active database is not the expected local if_dashboard database.');

            return self::FAILURE;
        }

        $path = base_path($this->option('path') ?: 'docs/data/real-dataEIM_2025_import_csv');

        try {
            $files = $this->readAndValidatePackage($path);
            $this->info('CSV validation passed. Existing records will not be overwritten; imported entity rows will be drafts.');
            foreach ($files as $name => $rows) {
                $this->line(sprintf('%-42s %d rows', $name, count($rows)));
            }

            if ($this->option('dry-run')) {
                $this->info('Dry run complete; database unchanged.');

                return self::SUCCESS;
            }

            DB::transaction(fn () => $this->importPackage($files));
            $this->info('Import committed. No existing record was deleted or overwritten.');
            $this->table(['Table', 'Imported rows'], [
                ['issue_records', count($files['issue-record-importer.csv'])],
                ['intervention_records', count($files['intervention-record-importer.csv'])],
                ['resource_records', count($files['resource-record-importer.csv'])],
                ['law_policy_records', count($files['law-policy-record-importer.csv'])],
                ['issue record links', count($files['issue-record-relationship-importer.csv'])],
                ['eim_record_relationships', count($files['record-relationship-importer.csv'])],
                ['eim_actors', count($files['actor-importer.csv'])],
                ['eim_actor_links', count($files['actor-link-importer.csv'])],
            ]);

            return self::SUCCESS;
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    private function readAndValidatePackage(string $path): array
    {
        $spec = [
            'issue-record-importer.csv' => ['issue_records', ['record_key', 'issue_area', 'indicator', 'state']],
            'intervention-record-importer.csv' => ['intervention_records', ['record_key', 'issue_area', 'state', 'name', 'intervention_type']],
            'resource-record-importer.csv' => ['resource_records', ['record_key', 'issue_area', 'state', 'funder_mda', 'resource_category', 'amount', 'currency']],
            'law-policy-record-importer.csv' => ['law_policy_records', ['record_key', 'issue_area', 'instrument_title', 'instrument_category', 'jurisdiction', 'status_label']],
            'issue-record-relationship-importer.csv' => [null, ['issue_record_key', 'related_record_type', 'related_record_key', 'relationship_type', 'source_reference']],
            'record-relationship-importer.csv' => [null, ['from_record_type', 'from_record_key', 'to_record_type', 'to_record_key', 'relationship_type']],
            'actor-importer.csv' => [null, ['actor_key', 'actor_name', 'actor_type']],
            'actor-link-importer.csv' => [null, ['actor_key', 'role', 'linked_record_type', 'linked_record_key']],
        ];

        $files = [];
        foreach ($spec as $filename => [$table, $required]) {
            $fullPath = $path.'/'.$filename;
            if (! is_file($fullPath)) {
                throw new RuntimeException("Missing required package file: {$filename}");
            }
            if ($table && ! Schema::hasTable($table)) {
                throw new RuntimeException("Required table {$table} is missing. Run migrations first.");
            }

            $files[$filename] = $this->readCsv($fullPath, $required);
        }

        foreach (['eim_record_relationships', 'eim_actors', 'eim_actor_links'] as $table) {
            if (! Schema::hasTable($table)) {
                throw new RuntimeException("Required table {$table} is missing. Run migrations first.");
            }
        }

        foreach (['issue-record-importer.csv' => 'issue', 'intervention-record-importer.csv' => 'intervention', 'resource-record-importer.csv' => 'resource', 'law-policy-record-importer.csv' => 'law_policy'] as $file => $type) {
            foreach ($files[$file] as $row) {
                $key = trim($row['record_key'] ?? '');
                if ($key === '' || isset($this->recordKeys[$type][$key])) {
                    throw new RuntimeException("Missing or duplicate {$type} record_key in {$file}: {$key}");
                }
                $this->recordKeys[$type][$key] = true;
            }
        }

        foreach ($files['issue-record-relationship-importer.csv'] as $row) {
            $this->assertKnownRecord('issue', $row['issue_record_key'] ?? '', 'issue relationship source');
            $this->assertKnownRecord($row['related_record_type'] ?? '', $row['related_record_key'] ?? '', 'issue relationship target');
        }
        foreach ($files['record-relationship-importer.csv'] as $row) {
            $this->assertKnownRecord($row['from_record_type'] ?? '', $row['from_record_key'] ?? '', 'relationship source');
            $this->assertKnownRecord($row['to_record_type'] ?? '', $row['to_record_key'] ?? '', 'relationship target');
        }

        $actorKeys = [];
        foreach ($files['actor-importer.csv'] as $row) {
            $actorKey = trim($row['actor_key'] ?? '');
            if ($actorKey === '' || isset($actorKeys[$actorKey])) {
                throw new RuntimeException("Missing or duplicate actor_key: {$actorKey}");
            }
            $actorKeys[$actorKey] = true;
        }
        foreach ($files['actor-link-importer.csv'] as $row) {
            if (! isset($actorKeys[trim($row['actor_key'] ?? '')])) {
                throw new RuntimeException('Actor link refers to an actor absent from actor-importer.csv: '.($row['actor_key'] ?? ''));
            }
            $this->assertKnownRecord($row['linked_record_type'] ?? '', $row['linked_record_key'] ?? '', 'actor link target');
        }

        return $files;
    }

    private function readCsv(string $path, array $required): array
    {
        $handle = fopen($path, 'rb');
        if (! $handle) {
            throw new RuntimeException('Unable to read '.$path);
        }
        $headers = fgetcsv($handle, escape: '\\');
        if (! $headers) {
            fclose($handle);
            throw new RuntimeException('CSV has no header row: '.$path);
        }
        $headers = array_map(fn ($header) => trim(preg_replace('/^\xEF\xBB\xBF/', '', (string) $header)), $headers);
        if (array_diff($required, $headers)) {
            fclose($handle);
            throw new RuntimeException('CSV is missing required headers in '.basename($path).': '.implode(', ', array_diff($required, $headers)));
        }

        $rows = [];
        $line = 1;
        while (($values = fgetcsv($handle, escape: '\\')) !== false) {
            $line++;
            if ($values === [null]) {
                continue;
            }
            if (count($values) !== count($headers)) {
                fclose($handle);
                throw new RuntimeException('Malformed CSV row in '.basename($path).'; field count does not match headers.');
            }
            $row = array_combine($headers, $values);
            $row = array_map(static function ($value) {
                if (! is_string($value)) {
                    return $value;
                }

                $value = trim($value);

                return $value === '' ? null : $value;
            }, $row);
            foreach ($required as $requiredColumn) {
                if (($row[$requiredColumn] ?? null) === null) {
                    fclose($handle);
                    throw new RuntimeException('Required value is blank in '.basename($path).' at data row '.($line - 1).': '.$requiredColumn);
                }
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    private function assertKnownRecord(string $type, string $key, string $context): void
    {
        $type = strtolower(trim($type));
        $key = trim($key);
        if (! isset($this->recordTables[$type]) || $key === '') {
            throw new RuntimeException("Unknown record type or empty key in {$context}: {$type}/{$key}");
        }
        if (! isset($this->recordKeys[$type][$key]) && ! ($type === 'law' && isset($this->recordKeys['law_policy'][$key]))) {
            throw new RuntimeException("Unresolved {$context} endpoint: {$type}/{$key}");
        }
    }

    private function importPackage(array $files): void
    {
        $entityFiles = [
            'issue-record-importer.csv' => 'issue_records',
            'intervention-record-importer.csv' => 'intervention_records',
            'resource-record-importer.csv' => 'resource_records',
            'law-policy-record-importer.csv' => 'law_policy_records',
        ];
        foreach ($entityFiles as $file => $table) {
            foreach ($files[$file] as $row) {
                $row['status'] = 'draft';
                $row['published_at'] = null;
                $exists = DB::table($table)->where('record_key', $row['record_key'])->exists();
                if (! $exists) {
                    DB::table($table)->insert($row + ['created_at' => now(), 'updated_at' => now()]);
                }
            }
        }

        foreach ($files['issue-record-relationship-importer.csv'] as $row) {
            $issueId = DB::table('issue_records')->where('record_key', $row['issue_record_key'])->value('id');
            $type = $row['related_record_type'] === 'law' ? 'law_policy' : $row['related_record_type'];
            $table = $this->recordTables[$type];
            $targetId = DB::table($table)->where('record_key', $row['related_record_key'])->value('id');
            $pivot = match ($type) {
                'intervention' => ['issue_intervention_links', 'intervention_record_id'],
                'resource' => ['issue_resource_links', 'resource_record_id'],
                'law_policy' => ['issue_law_policy_links', 'law_policy_record_id'],
            };
            DB::table($pivot[0])->updateOrInsert(
                ['issue_record_id' => $issueId, $pivot[1] => $targetId],
                ['relationship_type' => $row['relationship_type'], 'source_reference' => $row['source_reference'], 'created_at' => now(), 'updated_at' => now()],
            );
        }

        foreach ($files['record-relationship-importer.csv'] as $row) {
            $hash = hash('sha256', implode('|', [$row['from_record_type'], $row['from_record_key'], $row['to_record_type'], $row['to_record_key'], $row['relationship_type']]));
            DB::table('eim_record_relationships')->updateOrInsert(['relationship_key' => $hash], $row + ['created_at' => now(), 'updated_at' => now()]);
        }

        foreach ($files['actor-importer.csv'] as $row) {
            DB::table('eim_actors')->updateOrInsert(['actor_key' => $row['actor_key']], $row + ['created_at' => now(), 'updated_at' => now()]);
        }

        foreach ($files['actor-link-importer.csv'] as $row) {
            $actorId = DB::table('eim_actors')->where('actor_key', $row['actor_key'])->value('id');
            $link = collect($row)->except('actor_key')->all();
            DB::table('eim_actor_links')->updateOrInsert(
                ['actor_id' => $actorId, 'role' => $row['role'], 'linked_record_type' => $row['linked_record_type'], 'linked_record_key' => $row['linked_record_key']],
                collect($link)->except(['role', 'linked_record_type', 'linked_record_key'])->merge(['updated_at' => now(), 'created_at' => now()])->all(),
            );
        }

        foreach (['eim:public-records:v2', 'eim:public-records:v3'] as $key) {
            cache()->forget($key);
        }
    }
}
