<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_sees_sales_navigation_but_not_smd_navigation(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'role' => User::ROLE_SALES,
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($sales)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Kunjungan Sales');
        $response->assertDontSee('Kunjungan SMD');
    }

    public function test_smd_sees_smd_navigation_but_not_sales_navigation(): void
    {
        $branch = Branch::factory()->create();
        $smd = User::factory()->create([
            'role' => User::ROLE_SMD,
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($smd)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Kunjungan SMD');
        $response->assertDontSee('Kunjungan Sales');
    }

    public function test_supervisor_sees_both_visit_navigation_items(): void
    {
        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create([
            'role' => User::ROLE_SUPERVISOR,
            'branch_id' => $branch->id,
        ]);

        $response = $this->actingAs($supervisor)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Kunjungan Sales');
        $response->assertSee('Kunjungan SMD');
    }
}
