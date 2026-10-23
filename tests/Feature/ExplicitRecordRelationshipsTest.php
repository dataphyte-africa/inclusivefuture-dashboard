<?php

namespace Tests\Feature;

use App\Filament\Imports\IssueRecordRelationshipImporter;
use App\Http\Controllers\DashboardController;
use App\Models\InterventionRecord;
use App\Models\IssueInterventionLink;
use App\Models\IssueRecord;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use App\Models\User;
use Database\Seeders\DevelopmentEvidenceCompletenessSeeder;
use Database\Seeders\TestingEvidenceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ExplicitRecordRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_testing_seed_fills_optional_fields_and_exposes_explicit_links_in_dashboard_and_export(): void
    {
        $this->seed(TestingEvidenceSeeder::class);
        Cache::forget('eim:public-records:v3');

        $records = app(DashboardController::class)->publishedRecords();
        $issue = $records->firstWhere('record_key', 'test-issue-complete');
        $this->assertNotNull($issue);
        $this->assertSame('2024', $issue['details']['period']);
        $this->assertSame('https://testing.com/sources/test-issue-complete', $issue['details']['source_url']);
        $this->assertEqualsCanonicalizing(['intervention', 'intervention', 'resource', 'resource', 'law', 'law'], array_column($issue['related_records'], 'type'));
        $this->assertSame('responds_to', collect($issue['related_records'])->firstWhere('type', 'intervention')['relationship_type']);
        $this->assertSame('https://testing.com/relationships/test-issue-intervention', collect($issue['related_records'])->firstWhere('type', 'intervention')['source_reference']);

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->actingAs($admin)->get('/admin/issue-records/'.IssueRecord::where('record_key', 'test-issue-complete')->value('id').'/edit')
            ->assertOk()
            ->assertSee('Direct intervention relationships')
            ->assertSee('Direct resource relationships')
            ->assertSee('Direct law and policy relationships');
        $this->actingAs($admin)->get('/admin/issue-records')->assertOk()->assertSee('Import record relationships');

        $intervention = $records->firstWhere('record_key', 'test-intervention-complete');
        $this->assertSame('2024-01-01', $intervention['details']['start_date']);
        $this->assertSame('2024-12-31', $intervention['details']['end_date']);
        $this->assertEqualsCanonicalizing(
            ['test-issue-complete', 'test-issue-related-second'],
            array_column($intervention['related_records'], 'record_key'),
        );

        $secondIssue = $records->firstWhere('record_key', 'test-issue-related-second');
        $this->assertEqualsCanonicalizing(['intervention', 'resource', 'law'], array_column($secondIssue['related_records'], 'type'));
        $this->assertSame([], $records->firstWhere('record_key', 'test-resource-unlinked-control')['related_records']);
        $this->assertSame([], $records->firstWhere('record_key', 'test-law-unlinked-control')['related_records']);
        $unlinkedIssue = $records->firstWhere('record_key', 'test-issue-unlinked-control');
        $this->assertSame([], $unlinkedIssue['related_records']);
        $cooccurringIntervention = $records->firstWhere('record_key', 'test-intervention-unlinked-control');
        $this->assertSame([], $cooccurringIntervention['related_records']);

        $this->get('/')->assertOk()->assertSee('test-issue-complete')->assertSee('related_records');
        $csv = $this->get('/download-data.csv')->assertOk()->streamedContent();
        $this->assertStringContainsString('related_records_json', $csv);
        $this->assertStringContainsString('https://testing.com/relationships/test-issue-resource', $csv);
    }

    public function test_relationship_csv_import_creates_or_updates_links_and_rejects_unknown_targets(): void
    {
        $this->seed(TestingEvidenceSeeder::class);

        $rows = [
            [
                'issue_record_key' => 'test-issue-unlinked-control',
                'related_record_type' => 'intervention',
                'related_record_key' => 'test-intervention-unlinked-control',
                'relationship_type' => 'responds_to',
                'source_reference' => 'https://testing.com/relationships/test-imported-intervention-link',
            ],
            [
                'issue_record_key' => 'test-issue-unlinked-control',
                'related_record_type' => 'resource',
                'related_record_key' => 'test-resource-unlinked-control',
                'relationship_type' => 'funds',
                'source_reference' => 'https://testing.com/relationships/test-imported-resource-link',
            ],
            [
                'issue_record_key' => 'test-issue-unlinked-control',
                'related_record_type' => 'law',
                'related_record_key' => 'test-law-unlinked-control',
                'relationship_type' => 'governed_by',
                'source_reference' => 'https://testing.com/relationships/test-imported-law-link',
            ],
        ];

        foreach ($rows as $row) {
            IssueRecordRelationshipImporter::test()->import($row)->assertImported();
            IssueRecordRelationshipImporter::test()->import($row)->assertImported();
        }

        $issueId = IssueRecord::where('record_key', $rows[0]['issue_record_key'])->value('id');
        $interventionId = InterventionRecord::where('record_key', $rows[0]['related_record_key'])->value('id');
        $this->assertDatabaseHas('issue_intervention_links', [
            'issue_record_id' => $issueId,
            'intervention_record_id' => $interventionId,
            'relationship_type' => 'responds_to',
            'source_reference' => $rows[0]['source_reference'],
        ]);
        $this->assertSame(4, IssueInterventionLink::count());
        $this->assertDatabaseHas('issue_resource_links', [
            'issue_record_id' => $issueId,
            'resource_record_id' => ResourceRecord::where('record_key', $rows[1]['related_record_key'])->value('id'),
            'source_reference' => $rows[1]['source_reference'],
        ]);
        $this->assertDatabaseHas('issue_law_policy_links', [
            'issue_record_id' => $issueId,
            'law_policy_record_id' => LawPolicyRecord::where('record_key', $rows[2]['related_record_key'])->value('id'),
            'source_reference' => $rows[2]['source_reference'],
        ]);
        $this->assertSame(4, DB::table('issue_resource_links')->count());
        $this->assertSame(4, DB::table('issue_law_policy_links')->count());

        IssueRecordRelationshipImporter::test()->import([
            ...$rows[0],
            'related_record_key' => 'does-not-exist',
        ])->assertHasErrors('related_record_key');

        IssueRecordRelationshipImporter::test()->import([
            ...$rows[0],
            'related_record_type' => 'unrecognized',
        ])->assertHasErrors('related_record_type');

        IssueRecordRelationshipImporter::test()->import([
            ...$rows[0],
            'source_reference' => '',
        ])->assertHasErrors('source_reference');
    }

    public function test_development_completeness_seed_fills_blanks_without_overwriting_existing_values(): void
    {
        $issue = IssueRecord::create([
            'record_key' => 'dev-completeness-issue', 'issue_area' => 'Education', 'indicator' => 'Attendance',
            'state' => 'Lagos', 'value' => 12, 'unit' => '%', 'period' => '2022', 'source_name' => 'Preserve this source',
            'status' => 'published',
        ]);
        $intervention = InterventionRecord::create([
            'record_key' => 'dev-completeness-intervention', 'issue_area' => 'Education', 'state' => 'Lagos',
            'name' => 'Programme', 'intervention_type' => 'Service', 'status' => 'published',
        ]);
        $resource = ResourceRecord::create([
            'record_key' => 'dev-completeness-resource', 'issue_area' => 'Education', 'state' => 'Lagos',
            'funder_mda' => 'Agency', 'resource_category' => 'Budget allocation', 'amount' => 1000, 'status' => 'published',
        ]);
        $law = LawPolicyRecord::create([
            'record_key' => 'dev-completeness-law', 'issue_area' => 'Education', 'instrument_title' => 'Policy',
            'instrument_category' => 'Policy', 'status_label' => 'Active', 'status' => 'published',
        ]);

        $this->seed(DevelopmentEvidenceCompletenessSeeder::class);

        $issue->refresh();
        $this->assertSame('2022', $issue->period);
        $this->assertSame('Preserve this source', $issue->source_name);
        $this->assertStringStartsWith('https://testing.com/eim-development/issue/', $issue->source_url);
        $this->assertNotEmpty($issue->population_group);
        $this->assertNotEmpty($issue->disaggregation);

        $intervention->refresh();
        $this->assertMatchesRegularExpression('/^20\d{2}-\d{2}-\d{2}$/', $intervention->start_date);
        $this->assertMatchesRegularExpression('/^20\d{2}-\d{2}-\d{2}$/', $intervention->end_date);
        $this->assertContains($intervention->implementation_status, ['Planned', 'Active', 'Completed', 'Paused']);

        $resource->refresh();
        $this->assertNotEmpty($resource->programme);
        $this->assertContains($resource->funding_stage, ['Allocation', 'Commitment', 'Release']);
        $this->assertNotEmpty($resource->period);
        $this->assertNotNull($resource->allocation_year);

        $law->refresh();
        $this->assertMatchesRegularExpression('/^20\d{2}-\d{2}-\d{2}$/', $law->enactment_date);
        $this->assertSame('Development fixture source (not an external citation)', $intervention->source_name);
        $this->assertSame('Development fixture source (not an external citation)', $resource->source_name);
        $this->assertSame('Development fixture source (not an external citation)', $law->source_name);
        foreach ([$issue, $intervention, $resource, $law] as $record) {
            $this->assertStringStartsWith('https://testing.com/eim-development/', $record->source_url);
            $this->assertStringStartsWith('DEV-FIXTURE-', $record->source_reference);
        }
    }

    public function test_draft_linked_records_are_not_exposed_as_published_relationships(): void
    {
        $this->seed(TestingEvidenceSeeder::class);
        $issue = IssueRecord::where('record_key', 'test-issue-complete')->firstOrFail();
        $draft = InterventionRecord::create([
            'record_key' => 'test-intervention-draft', 'issue_area' => "Girls' Education", 'state' => 'Lagos',
            'name' => 'Draft intervention', 'intervention_type' => 'Service delivery', 'status' => 'draft',
        ]);
        IssueInterventionLink::create([
            'issue_record_id' => $issue->id, 'intervention_record_id' => $draft->id,
            'relationship_type' => 'responds_to', 'source_reference' => 'https://testing.com/relationships/draft-link',
        ]);

        Cache::forget('eim:public-records:v3');
        $records = app(DashboardController::class)->publishedRecords();
        $issueData = $records->firstWhere('record_key', 'test-issue-complete');
        $linkedKeys = collect($issueData['related_records'])->pluck('record_key');

        $this->assertNotContains('test-intervention-draft', $linkedKeys);
    }
}
