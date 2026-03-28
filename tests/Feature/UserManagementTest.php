<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_with_branch_assignment(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN_PUSAT,
            'branch_id' => null,
        ]);
        $branch = Branch::factory()->create();

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'name' => 'Supervisor Bali',
            'username' => 'supervisorbali',
            'email' => 'supervisor.bali@sfa.test',
            'role' => User::ROLE_SUPERVISOR,
            'branch_id' => $branch->id,
            'is_active' => 1,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'username' => 'supervisorbali',
            'role' => User::ROLE_SUPERVISOR,
            'branch_id' => $branch->id,
        ]);
    }

    public function test_non_admin_cannot_access_user_master(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'role' => User::ROLE_SALES,
            'branch_id' => $branch->id,
        ]);

        $this->actingAs($sales)
            ->get(route('users.index'))
            ->assertForbidden();
    }
}
