<?php

namespace Tests\Feature;

use App\Models\InterventionRecord;
use App\Models\IssueRecord;
use App\Models\LawPolicyRecord;
use App\Models\ResourceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardPublishedDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_includes_published_control_panel_records_and_excludes_drafts(): void
    {
        IssueRecord::create([
            'record_key' => 'cp-visible-issue',
            'issue_area' => 'Dashboard test issue',
            'indicator' => 'Test measure',
            'state' => 'Lagos',
            'value' => 12,
            'unit' => 'percent',
            'status' => 'published',
        ]);
        IssueRecord::create([
            'record_key' => 'cp-draft-issue',
            'issue_area' => 'Dashboard test issue',
            'indicator' => 'Draft measure',
            'state' => 'Kano',
            'status' => 'draft',
        ]);
        InterventionRecord::create([
            'record_key' => 'cp-visible-intervention',
            'issue_area' => 'Dashboard test issue',
            'state' => 'Lagos',
            'name' => 'Published test intervention',
            'intervention_type' => 'Service Delivery',
            'status' => 'published',
        ]);
        ResourceRecord::create([
            'record_key' => 'cp-visible-resource',
            'issue_area' => 'Dashboard test issue',
            'state' => 'Lagos',
            'funder_mda' => 'Test funder',
            'resource_category' => 'Government budget allocation',
            'amount' => 9876,
            'currency' => 'NGN',
            'status' => 'published',
        ]);
        LawPolicyRecord::create([
            'record_key' => 'cp-visible-law',
            'issue_area' => 'Dashboard test issue',
            'state' => 'Lagos',
            'instrument_title' => 'Published test law',
            'instrument_category' => 'Legislation',
            'jurisdiction' => 'State',
            'status_label' => 'Active',
            'status' => 'published',
        ]);

        Cache::forget('eim:public-records:v2');

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('cp-visible-issue')
            ->assertSee('cp-visible-intervention')
            ->assertSee('cp-visible-resource')
            ->assertSee('cp-visible-law')
            ->assertDontSee('cp-draft-issue');
    }
}
