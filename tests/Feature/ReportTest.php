<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\SalesVisitDetail;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_view_sales_report(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create(['role' => User::ROLE_SALES, 'branch_id' => $branch->id]);
        $outlet = Outlet::factory()->create(['branch_id' => $branch->id, 'created_by' => $sales->id]);

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

        $this->actingAs($sales)
            ->get(route('reports.index', ['type' => 'sales']))
            ->assertOk()
            ->assertSee('Laporan Sales');
    }

    public function test_supervisor_can_export_noo_related_outlet_report(): void
    {
        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create(['role' => User::ROLE_SUPERVISOR, 'branch_id' => $branch->id]);
        Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $supervisor->id,
            'name' => 'Outlet Report',
        ]);

        $this->actingAs($supervisor)
            ->get(route('reports.export', ['type' => 'outlets']))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_filter_sales_report_by_branch_and_user(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN_PUSAT, 'branch_id' => null]);
        $branchA = Branch::factory()->create(['name' => 'Cabang A']);
        $branchB = Branch::factory()->create(['name' => 'Cabang B']);
        $salesA = User::factory()->create(['role' => User::ROLE_SALES, 'branch_id' => $branchA->id, 'name' => 'Sales A']);
        $salesB = User::factory()->create(['role' => User::ROLE_SALES, 'branch_id' => $branchB->id, 'name' => 'Sales B']);
        $outletA = Outlet::factory()->create(['branch_id' => $branchA->id, 'created_by' => $salesA->id, 'name' => 'Outlet A']);
        $outletB = Outlet::factory()->create(['branch_id' => $branchB->id, 'created_by' => $salesB->id, 'name' => 'Outlet B']);

        $visitA = Visit::create([
            'branch_id' => $branchA->id,
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
            'branch_id' => $branchB->id,
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

        $this->actingAs($admin)
            ->get(route('reports.index', ['type' => 'sales', 'branch_id' => $branchA->id, 'user_id' => $salesA->id]))
            ->assertOk()
            ->assertSee('Outlet A')
            ->assertDontSee('Outlet B');
    }
}
