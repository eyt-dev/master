<?php

namespace Tests\Feature\Add2Farm;

use App\Models\Admin;
use App\Models\UpgradeRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RoleUpgradeTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['Admin', 'PublicVendor', 'PrivateVendor', 'User'] as $name) {
            Role::create(['name' => $name, 'guard_name' => 'admin']);
        }
    }

    private int $mobileCounter = 1000;

    private function makeUser(int $type): Admin
    {
        $roleName = [1 => 'Admin', 2 => 'PublicVendor', 3 => 'PrivateVendor', 4 => 'User'][$type];

        $admin = Admin::create([
            'name'          => "User {$type}",
            'mobile_number' => (string) (++$this->mobileCounter),
            'password'      => Hash::make('password123'),
            'type'          => $type,
            'status'        => 'Active',
            'created_from'  => 3,
        ]);

        $admin->assignRole($roleName);

        return $admin;
    }

    private function actingAsType(int $type): Admin
    {
        $user = $this->makeUser($type);
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->postJson('/api/add2farm/role/upgrade')->assertStatus(401);
        $this->postJson('/api/add2farm/role/downgrade')->assertStatus(401);
    }

    public function test_farm_owner_upgrade_creates_pending_request_without_changing_type(): void
    {
        $owner = $this->actingAsType(2);

        $this->postJson('/api/add2farm/role/upgrade')
            ->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'Pending');

        $this->assertDatabaseHas('upgrade_requests', [
            'admin_id' => $owner->id,
            'status'   => 'Pending',
        ]);
        $this->assertEquals(2, $owner->fresh()->type);
    }

    public function test_farm_owner_duplicate_pending_request_is_rejected(): void
    {
        $this->actingAsType(2);

        $this->postJson('/api/add2farm/role/upgrade')->assertStatus(201);
        $this->postJson('/api/add2farm/role/upgrade')
            ->assertStatus(409)
            ->assertJsonPath('success', false);

        $this->assertEquals(1, UpgradeRequest::count());
    }

    public function test_supervisor_upgrades_to_farm_owner_immediately(): void
    {
        $supervisor = $this->actingAsType(3);

        $this->postJson('/api/add2farm/role/upgrade')
            ->assertStatus(200)
            ->assertJsonPath('type', 2)
            ->assertJsonPath('type_label', 'Farm Owner');

        $supervisor->refresh();
        $this->assertEquals(2, $supervisor->type);
        $this->assertEquals('PublicVendor', $supervisor->roles->first()->name);
    }

    public function test_farmer_upgrades_to_supervisor_immediately(): void
    {
        $farmer = $this->actingAsType(4);

        $this->postJson('/api/add2farm/role/upgrade')
            ->assertStatus(200)
            ->assertJsonPath('type', 3);

        $farmer->refresh();
        $this->assertEquals(3, $farmer->type);
        $this->assertEquals('PrivateVendor', $farmer->roles->first()->name);
    }

    public function test_supervisor_downgrades_to_farmer(): void
    {
        $supervisor = $this->actingAsType(3);

        $this->postJson('/api/add2farm/role/downgrade')
            ->assertStatus(200)
            ->assertJsonPath('type', 4)
            ->assertJsonPath('type_label', 'Farmer');

        $supervisor->refresh();
        $this->assertEquals(4, $supervisor->type);
        $this->assertEquals('User', $supervisor->roles->first()->name);
    }

    public function test_farmer_cannot_downgrade(): void
    {
        $farmer = $this->actingAsType(4);

        $this->postJson('/api/add2farm/role/downgrade')->assertStatus(403);
        $this->assertEquals(4, $farmer->fresh()->type);
    }

    public function test_farm_owner_cannot_downgrade(): void
    {
        $owner = $this->actingAsType(2);

        $this->postJson('/api/add2farm/role/downgrade')->assertStatus(403);
        $this->assertEquals(2, $owner->fresh()->type);
    }

    public function test_farm_admin_cannot_upgrade_or_downgrade(): void
    {
        $admin = $this->actingAsType(1);

        $this->postJson('/api/add2farm/role/upgrade')->assertStatus(403);
        $this->postJson('/api/add2farm/role/downgrade')->assertStatus(403);
        $this->assertEquals(1, $admin->fresh()->type);
        $this->assertEquals(0, UpgradeRequest::count());
    }

    public function test_farm_admin_lists_only_pending_requests(): void
    {
        $owner = $this->makeUser(2);
        $pending = UpgradeRequest::create(['admin_id' => $owner->id, 'status' => 'Pending']);
        UpgradeRequest::create(['admin_id' => $owner->id, 'status' => 'Approved']);

        Sanctum::actingAs($this->makeUser(1));

        $this->getJson('/api/add2farm/role/upgrade-requests')
            ->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.data')
            ->assertJsonPath('data.data.0.id', $pending->id)
            ->assertJsonPath('data.data.0.user.id', $owner->id)
            ->assertJsonPath('data.data.0.user.type_label', 'Farm Owner');
    }

    public function test_only_farm_admin_can_list_or_approve_requests(): void
    {
        $owner = $this->makeUser(2);
        $request = UpgradeRequest::create(['admin_id' => $owner->id, 'status' => 'Pending']);

        Sanctum::actingAs($owner);
        $this->getJson('/api/add2farm/role/upgrade-requests')->assertStatus(403);
        $this->postJson("/api/add2farm/role/upgrade-requests/{$request->id}/approve")->assertStatus(403);

        Sanctum::actingAs($this->makeUser(3));
        $this->getJson('/api/add2farm/role/upgrade-requests')->assertStatus(403);
        $this->postJson("/api/add2farm/role/upgrade-requests/{$request->id}/approve")->assertStatus(403);

        $this->assertEquals(2, $owner->fresh()->type);
        $this->assertEquals('Pending', $request->fresh()->status);
    }

    public function test_farm_admin_approval_upgrades_farm_owner(): void
    {
        $owner = $this->makeUser(2);
        $admin = $this->makeUser(1);
        $request = UpgradeRequest::create(['admin_id' => $owner->id, 'status' => 'Pending']);

        Sanctum::actingAs($admin);
        $this->postJson("/api/add2farm/role/upgrade-requests/{$request->id}/approve")
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $owner->refresh();
        $this->assertEquals(1, $owner->type);
        $this->assertEquals('Admin', $owner->roles->first()->name);

        $request->refresh();
        $this->assertEquals('Approved', $request->status);
        $this->assertEquals($admin->id, $request->approved_by);
        $this->assertNotNull($request->approved_at);
    }

    public function test_approved_request_cannot_be_approved_again(): void
    {
        $owner = $this->makeUser(2);
        $request = UpgradeRequest::create(['admin_id' => $owner->id, 'status' => 'Pending']);

        Sanctum::actingAs($this->makeUser(1));
        $this->postJson("/api/add2farm/role/upgrade-requests/{$request->id}/approve")->assertStatus(200);
        $this->postJson("/api/add2farm/role/upgrade-requests/{$request->id}/approve")
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_request_is_not_approved_when_user_is_no_longer_farm_owner(): void
    {
        $supervisor = $this->makeUser(3);
        $request = UpgradeRequest::create(['admin_id' => $supervisor->id, 'status' => 'Pending']);

        Sanctum::actingAs($this->makeUser(1));
        $this->postJson("/api/add2farm/role/upgrade-requests/{$request->id}/approve")
            ->assertStatus(422);

        $this->assertEquals(3, $supervisor->fresh()->type);
        $this->assertEquals('Pending', $request->fresh()->status);
    }

    public function test_unknown_request_returns_not_found(): void
    {
        Sanctum::actingAs($this->makeUser(1));

        $this->postJson('/api/add2farm/role/upgrade-requests/999/approve')->assertStatus(404);
    }

    public function test_upgraded_farm_admin_cannot_request_upgrade_again(): void
    {
        $owner = $this->makeUser(2);
        $request = UpgradeRequest::create(['admin_id' => $owner->id, 'status' => 'Pending']);

        Sanctum::actingAs($this->makeUser(1));
        $this->postJson("/api/add2farm/role/upgrade-requests/{$request->id}/approve")->assertStatus(200);

        // Now a Farm Admin, so further upgrades are rejected
        Sanctum::actingAs($owner->fresh());
        $this->postJson('/api/add2farm/role/upgrade')->assertStatus(403);
    }
}
