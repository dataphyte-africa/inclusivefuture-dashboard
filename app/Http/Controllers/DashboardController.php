<?php

namespace App\Http\Controllers;

use App\Models\InterventionRecord;
use App\Models\IssueRecord;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $records = $this->publishedRecords();
        $typeCounts = $records->countBy('type');

        $states = [
            ['Abia', 'SE'], ['Adamawa', 'NE'], ['Akwa Ibom', 'SS'], ['Anambra', 'SE'], ['Bauchi', 'NE'], ['Bayelsa', 'SS'],
            ['Benue', 'NC'], ['Borno', 'NE'], ['Cross River', 'SS'], ['Delta', 'SS'], ['Ebonyi', 'SE'], ['Edo', 'SS'],
            ['Ekiti', 'SW'], ['Enugu', 'SE'], ['Gombe', 'NE'], ['Imo', 'SE'], ['Jigawa', 'NW'], ['Kaduna', 'NW'],
            ['Kano', 'NW'], ['Katsina', 'NW'], ['Kebbi', 'NW'], ['Kogi', 'NC'], ['Kwara', 'NC'], ['Lagos', 'SW'],
            ['Nasarawa', 'NC'], ['Niger', 'NC'], ['Ogun', 'SW'], ['Ondo', 'SW'], ['Osun', 'SW'], ['Oyo', 'SW'],
            ['Plateau', 'NC'], ['Rivers', 'SS'], ['Sokoto', 'NW'], ['Taraba', 'NE'], ['Yobe', 'NE'], ['Zamfara', 'NW'], ['FCT (Abuja)', 'NC'],
        ];

        return view('atlas', [
            'records' => $records,
            'states' => $states,
            'issueCount' => $records->where('type', 'issue')->pluck('issue_area')->unique()->count(),
            'stateCount' => $records->pluck('state')->filter()->unique()->count(),
            'types' => ['issue' => $typeCounts->get('issue', 0), 'intervention' => $typeCounts->get('intervention', 0), 'resource' => $typeCounts->get('resource', 0), 'law' => $typeCounts->get('law', 0)],
        ]);
    }

    public function download()
    {
        $records = $this->publishedRecords();

        return response()->streamDownload(function () use ($records) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['record_key', 'record_type', 'issue_area', 'state', 'title', 'details_json', 'related_records_json'], ',', '"', '');

            foreach ($records as $record) {
                fputcsv($output, [
                    $record['record_key'],
                    $record['type'],
                    $record['issue_area'],
                    $record['state'],
                    $record['title'],
                    json_encode($record['details'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    json_encode($record['related_records'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                ], ',', '"', '');
            }

            fclose($output);
        }, 'wocro-published-evidence.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function publishedRecords()
    {
        $records = collect(Cache::remember('eim:public-records:v3', now()->addMinutes(5), function () {
            $issues = IssueRecord::query()->with([
                'interventionLinks.interventionRecord', 'resourceLinks.resourceRecord', 'lawPolicyLinks.lawPolicyRecord',
            ])->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();
            $interventions = InterventionRecord::query()->with('issueLinks.issueRecord')->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();
            $resources = ResourceRecord::query()->with('issueLinks.issueRecord')->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();
            $laws = LawPolicyRecord::query()->with('issueLinks.issueRecord')->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();

            return collect()
                ->concat($issues->map(fn ($row) => $this->shape($row, 'issue', $row->indicator, ['value', 'unit', 'geography_level', 'population_group', 'disaggregation', 'period', 'data_type', 'source_name', 'source_url', 'source_reference', 'notes'], $this->issueRelatedRecords($row))))
                ->concat($interventions->map(fn ($row) => $this->shape($row, 'intervention', $row->name, ['geography_level', 'intervention_type', 'implementer', 'target_population', 'start_date', 'end_date', 'coverage_geography', 'implementation_status', 'description', 'source_name', 'source_url', 'source_reference', 'notes'], $this->targetRelatedIssues($row->issueLinks, 'issueRecord'))))
                ->concat($resources->map(fn ($row) => $this->shape($row, 'resource', $row->programme ?: $row->resource_category, ['geography_level', 'programme', 'funder_mda', 'resource_category', 'funding_stage', 'amount', 'currency', 'allocation_year', 'period', 'source_name', 'source_url', 'source_reference', 'notes'], $this->targetRelatedIssues($row->issueLinks, 'issueRecord'))))
                ->concat($laws->map(fn ($row) => $this->shape($row, 'law', $row->instrument_title, ['geography_level', 'instrument_category', 'law_policy_family', 'jurisdiction', 'status_label', 'enactment_date', 'source_name', 'source_url', 'source_reference', 'notes'], $this->targetRelatedIssues($row->issueLinks, 'issueRecord'))))
                ->values()
                ->all();
        }));

        return $records->map(function (array $record) {
            $record['issue_area'] = $this->cleanPublicCopy($record['issue_area'] ?? null);
            $record['title'] = $this->cleanPublicCopy($record['title'] ?? null);
            $record['details'] = collect($record['details'] ?? [])->map(
                fn ($value, $field) => $field === 'source_url' ? $value : $this->cleanPublicCopy($value),
            )->all();

            return $record;
        })->values();
    }

    private function shape($row, string $type, string $title, array $fields, array $relatedRecords = []): array
    {
        $details = collect($fields)->mapWithKeys(fn ($field) => [$field => $row->{$field}])->all();

        return [
            'record_key' => $row->record_key,
            'type' => $type,
            'issue_area' => $row->issue_area,
            'state' => $row->state,
            'title' => $title,
            'details' => $details,
            'related_records' => $relatedRecords,
        ];
    }

    private function issueRelatedRecords(IssueRecord $issue): array
    {
        return collect()
            ->concat($issue->interventionLinks->map(fn ($link) => $this->relatedRecord($link->interventionRecord, 'intervention', $link->relationship_type, $link->source_reference)))
            ->concat($issue->resourceLinks->map(fn ($link) => $this->relatedRecord($link->resourceRecord, 'resource', $link->relationship_type, $link->source_reference)))
            ->concat($issue->lawPolicyLinks->map(fn ($link) => $this->relatedRecord($link->lawPolicyRecord, 'law', $link->relationship_type, $link->source_reference)))
            ->filter()->values()->all();
    }

    private function targetRelatedIssues($links, string $relation): array
    {
        return $links->map(fn ($link) => $this->relatedRecord($link->{$relation}, 'issue', $link->relationship_type, $link->source_reference))
            ->filter()->values()->all();
    }

    private function relatedRecord($related, string $type, string $relationshipType, ?string $sourceReference): ?array
    {
        if (! $related || $related->status !== 'published') {
            return null;
        }

        $title = match ($type) {
            'issue' => $related->indicator,
            'intervention' => $related->name,
            'resource' => $related->programme ?: $related->resource_category,
            'law' => $related->instrument_title,
        };

        return [
            'record_key' => $related->record_key,
            'type' => $type,
            'issue_area' => $related->issue_area,
            'state' => $related->state,
            'title' => $title,
            'relationship_type' => $relationshipType,
            'source_reference' => $sourceReference,
        ];
    }

    private function cleanPublicCopy(mixed $value): mixed
    {
        if (! is_string($value)) {
            return $value;
        }

        $value = preg_replace('/\s*\(\s*fictional\s*\)/i', '', $value);
        $value = preg_replace('/\s*,?\s*\bfictional\b/i', '', $value);
        $value = preg_replace('/\s+/u', ' ', $value);
        $value = preg_replace('/\s+([,.;:])/u', '$1', $value);
        $value = preg_replace('/^[\s,;:.–—-]+/u', '', $value);
        $value = preg_replace('/\(\s*\)/', '', $value);

        return trim($value);
    }
}
