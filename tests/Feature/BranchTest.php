<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_branch_with_timezone(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN_PUSAT,
            'branch_id' => null,
        ]);

        $response = $this->actingAs($admin)->post(route('branches.store'), [
            'code' => 'DPS',
            'name' => 'Cabang Bali',
            'city' => 'Denpasar',
            'timezone' => 'Asia/Makassar',
            'address' => 'Denpasar',
            'is_active' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('branches', [
            'code' => 'DPS',
            'timezone' => 'Asia/Makassar',
        ]);
    }

    public function test_non_admin_cannot_access_branch_master(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'role' => User::ROLE_SALES,
            'branch_id' => $branch->id,
        ]);

        $this->actingAs($sales)
            ->get(route('branches.index'))
            ->assertForbidden();
    }
}
