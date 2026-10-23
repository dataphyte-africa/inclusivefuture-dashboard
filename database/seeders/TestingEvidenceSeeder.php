<?php

namespace Database\Seeders;

use App\Models\InterventionRecord;
use App\Models\IssueInterventionLink;
use App\Models\IssueLawPolicyLink;
use App\Models\IssueRecord;
use App\Models\IssueResourceLink;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use Illuminate\Database\Seeder;
use LogicException;

class TestingEvidenceSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new LogicException('TestingEvidenceSeeder may only run in local or testing environments.');
        }

        $issue = IssueRecord::firstOrCreate(['record_key' => 'test-issue-complete'], [
            'issue_area' => "Girls' Education", 'indicator' => 'Test school participation rate',
            'state' => 'Lagos', 'geography_level' => 'State', 'value' => 42.5, 'unit' => '%', 'population_group' => 'Girls aged 10–17',
            'disaggregation' => 'All', 'period' => '2024', 'data_type' => 'Administrative', 'source_name' => 'Testing fixture source',
            'source_url' => 'https://testing.com/sources/test-issue-complete', 'source_reference' => 'TEST-ISSUE-2024',
            'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        $secondIssue = IssueRecord::firstOrCreate(['record_key' => 'test-issue-related-second'], [
            'issue_area' => "Girls' Education", 'indicator' => 'Test literacy rate',
            'state' => 'Lagos', 'geography_level' => 'State', 'value' => 81.2, 'unit' => '%', 'population_group' => 'Girls aged 10–17',
            'disaggregation' => 'All', 'period' => '2023', 'data_type' => 'Administrative', 'source_name' => 'Testing fixture source',
            'source_url' => 'https://testing.com/sources/test-issue-related-second', 'source_reference' => 'TEST-ISSUE-2023',
            'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        IssueRecord::firstOrCreate(['record_key' => 'test-issue-unlinked-control'], [
            'issue_area' => "Girls' Education", 'indicator' => 'Test school access control',
            'state' => 'Lagos', 'geography_level' => 'State', 'value' => 12.0, 'unit' => '%', 'population_group' => 'Girls aged 10–17',
            'disaggregation' => 'All', 'period' => '2024', 'data_type' => 'Administrative', 'source_name' => 'Testing fixture source',
            'source_url' => 'https://testing.com/sources/test-issue-unlinked-control', 'source_reference' => 'TEST-ISSUE-CONTROL',
            'notes' => 'Same-area control with no direct record links.', 'status' => 'published', 'published_at' => now(),
        ]);

        $intervention = InterventionRecord::firstOrCreate(['record_key' => 'test-intervention-complete'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'name' => 'Test school access programme',
            'geography_level' => 'State', 'intervention_type' => 'Service delivery', 'implementer' => 'Testing fixture agency',
            'target_population' => 'Girls aged 10–17', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31',
            'coverage_geography' => 'Lagos', 'implementation_status' => 'Active', 'description' => 'Isolated development fixture.',
            'source_name' => 'Testing fixture source', 'source_url' => 'https://testing.com/sources/test-intervention-complete',
            'source_reference' => 'TEST-INTERVENTION-2024', 'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        InterventionRecord::firstOrCreate(['record_key' => 'test-intervention-unlinked-control'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'name' => 'Test co-occurring programme',
            'geography_level' => 'State', 'intervention_type' => 'Service delivery', 'implementer' => 'Testing fixture agency',
            'target_population' => 'Girls aged 10–17', 'start_date' => '2024-01-01', 'end_date' => '2024-12-31',
            'coverage_geography' => 'Lagos', 'implementation_status' => 'Active', 'description' => 'Same-area control with no direct issue link.',
            'source_name' => 'Testing fixture source', 'source_url' => 'https://testing.com/sources/test-intervention-unlinked-control',
            'source_reference' => 'TEST-INTERVENTION-CONTROL', 'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        $resource = ResourceRecord::firstOrCreate(['record_key' => 'test-resource-complete'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'geography_level' => 'State',
            'programme' => 'Test school access allocation', 'funder_mda' => 'Testing fixture agency', 'resource_category' => 'Budget allocation',
            'funding_stage' => 'Allocation', 'amount' => 125000, 'currency' => 'NGN', 'allocation_year' => 2024, 'period' => '2024',
            'source_name' => 'Testing fixture source', 'source_url' => 'https://testing.com/sources/test-resource-complete',
            'source_reference' => 'TEST-RESOURCE-2024', 'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        $law = LawPolicyRecord::firstOrCreate(['record_key' => 'test-law-complete'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'geography_level' => 'State',
            'instrument_title' => 'Test education policy', 'instrument_category' => 'Policy', 'law_policy_family' => 'Education',
            'jurisdiction' => 'State', 'status_label' => 'Active', 'enactment_date' => '2024-03-15', 'source_name' => 'Testing fixture source',
            'source_url' => 'https://testing.com/sources/test-law-complete', 'source_reference' => 'TEST-LAW-2024',
            'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        $secondIntervention = InterventionRecord::firstOrCreate(['record_key' => 'test-intervention-secondary'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'name' => 'Test school retention service',
            'geography_level' => 'State', 'intervention_type' => 'Service delivery', 'implementer' => 'Testing fixture agency',
            'target_population' => 'Girls aged 10–17', 'start_date' => '2023-01-01', 'end_date' => '2024-12-31',
            'coverage_geography' => 'Lagos', 'implementation_status' => 'Completed', 'description' => 'Isolated development fixture.',
            'source_name' => 'Testing fixture source', 'source_url' => 'https://testing.com/sources/test-intervention-secondary',
            'source_reference' => 'TEST-INTERVENTION-SECONDARY', 'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        $secondResource = ResourceRecord::firstOrCreate(['record_key' => 'test-resource-secondary'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'geography_level' => 'State',
            'programme' => 'Test school retention allocation', 'funder_mda' => 'Testing fixture agency', 'resource_category' => 'Grant funding',
            'funding_stage' => 'Commitment', 'amount' => 35000, 'currency' => 'USD', 'allocation_year' => 2023, 'period' => '2023',
            'source_name' => 'Testing fixture source', 'source_url' => 'https://testing.com/sources/test-resource-secondary',
            'source_reference' => 'TEST-RESOURCE-SECONDARY', 'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        $secondLaw = LawPolicyRecord::firstOrCreate(['record_key' => 'test-law-secondary'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'geography_level' => 'State',
            'instrument_title' => 'Test school retention framework', 'instrument_category' => 'Policy', 'law_policy_family' => 'Education',
            'jurisdiction' => 'State', 'status_label' => 'Active', 'enactment_date' => '2023-06-01', 'source_name' => 'Testing fixture source',
            'source_url' => 'https://testing.com/sources/test-law-secondary', 'source_reference' => 'TEST-LAW-SECONDARY',
            'notes' => 'Isolated development fixture.', 'status' => 'published', 'published_at' => now(),
        ]);

        $cooccurringResource = ResourceRecord::firstOrCreate(['record_key' => 'test-resource-unlinked-control'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'geography_level' => 'State',
            'programme' => 'Test co-occurring allocation', 'funder_mda' => 'Testing fixture agency', 'resource_category' => 'Budget allocation',
            'funding_stage' => 'Allocation', 'amount' => 10000, 'currency' => 'NGN', 'allocation_year' => 2024, 'period' => '2024',
            'source_name' => 'Testing fixture source', 'source_url' => 'https://testing.com/sources/test-resource-unlinked-control',
            'source_reference' => 'TEST-RESOURCE-CONTROL', 'notes' => 'Same-area control with no direct issue link.', 'status' => 'published', 'published_at' => now(),
        ]);

        $cooccurringLaw = LawPolicyRecord::firstOrCreate(['record_key' => 'test-law-unlinked-control'], [
            'issue_area' => "Girls' Education", 'state' => 'Lagos', 'geography_level' => 'State',
            'instrument_title' => 'Test co-occurring policy control', 'instrument_category' => 'Policy', 'law_policy_family' => 'Education',
            'jurisdiction' => 'State', 'status_label' => 'Active', 'enactment_date' => '2024-02-01', 'source_name' => 'Testing fixture source',
            'source_url' => 'https://testing.com/sources/test-law-unlinked-control', 'source_reference' => 'TEST-LAW-CONTROL',
            'notes' => 'Same-area control with no direct issue link.', 'status' => 'published', 'published_at' => now(),
        ]);

        foreach ([
            [$issue, $intervention, 'responds_to', 'test-issue-intervention'],
            [$secondIssue, $intervention, 'responds_to', 'test-issue-second-intervention'],
            [$issue, $secondIntervention, 'contributes_to', 'test-issue-secondary-intervention'],
        ] as [$issueRow, $interventionRow, $type, $reference]) {
            IssueInterventionLink::updateOrCreate(
                ['issue_record_id' => $issueRow->id, 'intervention_record_id' => $interventionRow->id],
                ['relationship_type' => $type, 'source_reference' => "https://testing.com/relationships/{$reference}"],
            );
        }

        foreach ([
            [$issue, $resource, 'funds', 'test-issue-resource'],
            [$secondIssue, $resource, 'supports', 'test-issue-second-resource'],
            [$issue, $secondResource, 'funds', 'test-issue-secondary-resource'],
        ] as [$issueRow, $resourceRow, $type, $reference]) {
            IssueResourceLink::updateOrCreate(
                ['issue_record_id' => $issueRow->id, 'resource_record_id' => $resourceRow->id],
                ['relationship_type' => $type, 'source_reference' => "https://testing.com/relationships/{$reference}"],
            );
        }

        foreach ([
            [$issue, $law, 'governs', 'test-issue-law'],
            [$secondIssue, $law, 'governs', 'test-issue-second-law'],
            [$issue, $secondLaw, 'supports', 'test-issue-secondary-law'],
        ] as [$issueRow, $lawRow, $type, $reference]) {
            IssueLawPolicyLink::updateOrCreate(
                ['issue_record_id' => $issueRow->id, 'law_policy_record_id' => $lawRow->id],
                ['relationship_type' => $type, 'source_reference' => "https://testing.com/relationships/{$reference}"],
            );
        }
    }
}
