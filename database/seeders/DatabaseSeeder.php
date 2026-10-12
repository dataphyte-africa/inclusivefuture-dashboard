<?php

namespace Database\Seeders;

use App\Models\IssueRecord;
use App\Models\InterventionRecord;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ([
            [IssueRecord::class, 'issues.csv'],
            [InterventionRecord::class, 'interventions.csv'],
            [ResourceRecord::class, 'resources.csv'],
            [LawPolicyRecord::class, 'laws-policies.csv'],
        ] as [$model, $file]) {
            $stream = fopen(base_path("data/samples/{$file}"), 'r');
            $headers = fgetcsv($stream, 0, ',', '"', '');

            while (($values = fgetcsv($stream, 0, ',', '"', '')) !== false) {
                $row = array_combine($headers, $values);
                $row['status'] = 'published';
                $row['published_at'] = now();
                $model::updateOrCreate(['record_key' => $row['record_key']], $row);
            }

            fclose($stream);
        }
    }
}
