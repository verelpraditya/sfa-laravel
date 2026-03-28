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
}
