<?php

namespace Tests\Feature;

use App\Models\IssueRecord;
use App\Models\User;
use App\Policies\IssueRecordPolicy;
use App\Policies\UserPolicy;
use Filament\Panel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_admins_can_manage_users_and_panel_login_rejects_unknown_roles(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $editor = User::factory()->create(['role' => User::ROLE_EDITOR]);
        $panel = \Mockery::mock(Panel::class);

        $policy = new UserPolicy;

        $this->assertTrue($policy->viewAny($admin));
        $this->assertTrue($policy->create($admin));
        $this->assertFalse($policy->viewAny($editor));
        $this->assertFalse($policy->create($editor));
        $this->assertTrue($admin->canAccessPanel($panel));
        $this->assertFalse((new User(['role' => 'unknown']))->canAccessPanel($panel));
        $this->assertInstanceOf(UserPolicy::class, Gate::getPolicyFor(User::class));

        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($editor)->get('/admin/users')->assertForbidden();
    }

    public function test_data_entry_can_edit_only_drafts_while_editors_can_publish_and_only_admins_can_delete(): void
    {
        $entry = new User(['role' => User::ROLE_DATA_ENTRY]);
        $editor = new User(['role' => User::ROLE_EDITOR]);
        $admin = new User(['role' => User::ROLE_ADMIN]);
        $draft = new IssueRecord(['status' => 'draft']);
        $published = new IssueRecord(['status' => 'published']);
        $policy = new IssueRecordPolicy;

        $this->assertTrue($policy->create($entry));
        $this->assertTrue($policy->update($entry, $draft));
        $this->assertFalse($policy->update($entry, $published));
        $this->assertTrue($policy->update($editor, $published));
        $this->assertFalse($policy->delete($editor, $draft));
        $this->assertTrue($policy->delete($admin, $draft));
    }

    public function test_password_is_hashed_when_created_or_reset(): void
    {
        $user = User::factory()->create(['password' => 'initial-password']);
        $initialHash = $user->password;

        $user->update(['password' => 'replacement-password']);
        $user->refresh();

        $this->assertNotSame('replacement-password', $user->password);
        $this->assertNotSame($initialHash, $user->password);
        $this->assertTrue(Hash::check('replacement-password', $user->password));
    }
}
