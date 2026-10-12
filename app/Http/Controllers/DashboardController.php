<?php

namespace App\Http\Controllers;

use App\Models\IssueRecord;
use App\Models\InterventionRecord;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $records = collect(Cache::remember('eim:public-records:v2', now()->addMinutes(5), function () {
            $issues = IssueRecord::query()->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();
            $interventions = InterventionRecord::query()->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();
            $resources = ResourceRecord::query()->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();
            $laws = LawPolicyRecord::query()->where('status', 'published')->orderBy('issue_area')->orderBy('state')->get();

            return collect()
                ->concat($issues->map(fn ($row) => $this->shape($row, 'issue', $row->indicator, ['value', 'unit', 'geography_level', 'population_group', 'disaggregation', 'period', 'data_type', 'source_name', 'source_url', 'source_reference', 'notes'])))
                ->concat($interventions->map(fn ($row) => $this->shape($row, 'intervention', $row->name, ['geography_level', 'intervention_type', 'implementer', 'target_population', 'start_date', 'end_date', 'coverage_geography', 'implementation_status', 'description', 'source_name', 'source_url', 'source_reference', 'notes'])))
                ->concat($resources->map(fn ($row) => $this->shape($row, 'resource', $row->programme ?: $row->resource_category, ['geography_level', 'programme', 'funder_mda', 'resource_category', 'funding_stage', 'amount', 'currency', 'allocation_year', 'period', 'source_name', 'source_url', 'source_reference', 'notes'])))
                ->concat($laws->map(fn ($row) => $this->shape($row, 'law', $row->instrument_title, ['geography_level', 'instrument_category', 'law_policy_family', 'jurisdiction', 'status_label', 'enactment_date', 'source_name', 'source_url', 'source_reference', 'notes'])))
                ->values()
                ->all();
        }));

        $typeCounts = $records->countBy('type');

        $states = [
            ['Abia', 'SE'], ['Adamawa', 'NE'], ['Akwa Ibom', 'SS'], ['Anambra', 'SE'], ['Bauchi', 'NE'], ['Bayelsa', 'SS'],
            ['Benue', 'NC'], ['Borno', 'NE'], ['Cross River', 'SS'], ['Delta', 'SS'], ['Ebonyi', 'SE'], ['Edo', 'SS'],
            ['Ekiti', 'SW'], ['Enugu', 'SE'], ['Gombe', 'NE'], ['Imo', 'SE'], ['Jigawa', 'NW'], ['Kaduna', 'NW'],
            ['Kano', 'NW'], ['Katsina', 'NW'], ['Kebbi', 'NW'], ['Kogi', 'NC'], ['Kwara', 'NC'], ['Lagos', 'SW'],
            ['Nasarawa', 'NC'], ['Niger', 'NC'], ['Ogun', 'SW'], ['Ondo', 'SW'], ['Osun', 'SW'], ['Oyo', 'SW'],
            ['Plateau', 'NC'], ['Rivers', 'SS'], ['Sokoto', 'NW'], ['Taraba', 'NE'], ['Yobe', 'NE'], ['Zamfara', 'NW'], ['FCT (Abuja)', 'NC'],
        ];

        return view('dashboard', [
            'records' => $records,
            'states' => $states,
            'issueCount' => $records->where('type', 'issue')->pluck('issue_area')->unique()->count(),
            'stateCount' => $records->pluck('state')->filter()->unique()->count(),
            'types' => ['issue' => $typeCounts->get('issue', 0), 'intervention' => $typeCounts->get('intervention', 0), 'resource' => $typeCounts->get('resource', 0), 'law' => $typeCounts->get('law', 0)],
        ]);
    }

    private function shape($row, string $type, string $title, array $fields): array
    {
        $details = collect($fields)->mapWithKeys(fn ($field) => [$field => $row->{$field}])->all();

        return [
            'record_key' => $row->record_key,
            'type' => $type,
            'issue_area' => $row->issue_area,
            'state' => $row->state,
            'title' => $title,
            'details' => $details,
        ];
    }
}
