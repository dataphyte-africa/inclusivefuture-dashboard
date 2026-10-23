<?php

namespace Tests\Feature;

use App\Filament\Resources\InterventionRecords\Pages\ListInterventionRecords;
use App\Models\InterventionRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InterventionBulkDraftActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_can_move_selected_interventions_to_draft_and_total_is_shown(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_EDITOR]));

        $record = InterventionRecord::create([
            'record_key' => 'bulk-draft-test',
            'issue_area' => "Girls' Education",
            'state' => 'Lagos',
            'name' => 'Test intervention',
            'intervention_type' => 'Service delivery',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Livewire::test(ListInterventionRecords::class)
            ->assertSee('1 total intervention record')
            ->assertTableBulkActionVisible('moveToDraft')
            ->callTableBulkAction('moveToDraft', records: [$record])
            ->callMountedTableBulkAction();

        $this->assertDatabaseHas('intervention_records', [
            'id' => $record->id,
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function test_data_entry_users_cannot_move_records_to_draft_in_bulk(): void
    {
        $this->actingAs(User::factory()->create(['role' => User::ROLE_DATA_ENTRY]));

        Livewire::test(ListInterventionRecords::class)
            ->assertTableBulkActionHidden('moveToDraft');
    }
}
