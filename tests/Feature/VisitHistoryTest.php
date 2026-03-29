<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\SalesVisitDetail;
use App\Models\SmdVisitDetail;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_supervisor_can_view_branch_visit_history(): void
    {
        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create(['role' => User::ROLE_SUPERVISOR, 'branch_id' => $branch->id]);
        $sales = User::factory()->create(['role' => User::ROLE_SALES, 'branch_id' => $branch->id, 'name' => 'Sales Cabang']);
        $outlet = Outlet::factory()->create(['branch_id' => $branch->id, 'created_by' => $sales->id, 'name' => 'Outlet Cabang']);

        $visit = Visit::create([
            'branch_id' => $branch->id,
            'outlet_id' => $outlet->id,
            'user_id' => $sales->id,
            'visit_type' => 'sales',
            'outlet_condition' => 'buka',
            'latitude' => -6.9,
            'longitude' => 107.6,
            'visit_photo_path' => 'test.jpg',
            'visited_at' => now(),
            'notes' => 'test',
        ]);

        SalesVisitDetail::create([
            'visit_id' => $visit->id,
            'order_amount' => 100000,
            'receivable_amount' => 50000,
        ]);

        $this->actingAs($supervisor)
            ->get(route('visit-history.index'))
            ->assertOk()
            ->assertSee('Outlet Cabang')
            ->assertSee('Sales Cabang');
    }

    public function test_sales_can_only_view_own_visit_history(): void
    {
        $branch = Branch::factory()->create();
        $salesA = User::factory()->create(['role' => User::ROLE_SALES, 'branch_id' => $branch->id, 'name' => 'Sales A']);
        $salesB = User::factory()->create(['role' => User::ROLE_SALES, 'branch_id' => $branch->id, 'name' => 'Sales B']);
        $outletA = Outlet::factory()->create(['branch_id' => $branch->id, 'created_by' => $salesA->id, 'name' => 'Outlet A']);
        $outletB = Outlet::factory()->create(['branch_id' => $branch->id, 'created_by' => $salesB->id, 'name' => 'Outlet B']);

        $visitA = Visit::create([
            'branch_id' => $branch->id,
            'outlet_id' => $outletA->id,
            'user_id' => $salesA->id,
            'visit_type' => 'sales',
            'outlet_condition' => 'buka',
            'latitude' => -6.9,
            'longitude' => 107.6,
            'visit_photo_path' => 'a.jpg',
            'visited_at' => now(),
            'notes' => 'a',
        ]);
        SalesVisitDetail::create(['visit_id' => $visitA->id, 'order_amount' => 1000, 'receivable_amount' => 500]);

        $visitB = Visit::create([
            'branch_id' => $branch->id,
            'outlet_id' => $outletB->id,
            'user_id' => $salesB->id,
            'visit_type' => 'sales',
            'outlet_condition' => 'buka',
            'latitude' => -6.9,
            'longitude' => 107.6,
            'visit_photo_path' => 'b.jpg',
            'visited_at' => now(),
            'notes' => 'b',
        ]);
        SalesVisitDetail::create(['visit_id' => $visitB->id, 'order_amount' => 2000, 'receivable_amount' => 1500]);

        $this->actingAs($salesA)
            ->get(route('visit-history.index'))
            ->assertOk()
            ->assertSee('Outlet A')
            ->assertDontSee('Outlet B');
    }

    public function test_smd_visit_detail_page_can_be_opened_by_owner(): void
    {
        $branch = Branch::factory()->create();
        $smd = User::factory()->create(['role' => User::ROLE_SMD, 'branch_id' => $branch->id]);
        $outlet = Outlet::factory()->create(['branch_id' => $branch->id, 'created_by' => $smd->id, 'name' => 'Outlet SMD']);

        $visit = Visit::create([
            'branch_id' => $branch->id,
            'outlet_id' => $outlet->id,
            'user_id' => $smd->id,
            'visit_type' => 'smd',
            'outlet_condition' => null,
            'latitude' => -6.9,
            'longitude' => 107.6,
            'visit_photo_path' => 'smd.jpg',
            'visited_at' => now(),
            'notes' => 'smd',
        ]);
        SmdVisitDetail::create(['visit_id' => $visit->id, 'po_amount' => 3000, 'payment_amount' => 1200]);

        $this->actingAs($smd)
            ->get(route('visit-history.show', $visit))
            ->assertOk()
            ->assertSee('Outlet SMD');
    }
}
