<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_only_see_outlets_from_their_own_branch(): void
    {
        $branchA = Branch::factory()->create(['name' => 'Cabang A']);
        $branchB = Branch::factory()->create(['name' => 'Cabang B']);

        $sales = User::factory()->create([
            'branch_id' => $branchA->id,
            'role' => User::ROLE_SALES,
        ]);

        $visibleOutlet = Outlet::factory()->create([
            'branch_id' => $branchA->id,
            'created_by' => $sales->id,
            'name' => 'Outlet Cabang A',
        ]);

        $hiddenOutlet = Outlet::factory()->create([
            'branch_id' => $branchB->id,
            'created_by' => $sales->id,
            'name' => 'Outlet Cabang B',
        ]);

        $response = $this->actingAs($sales)->get(route('outlets.index'));

        $response->assertOk();
        $response->assertSee($visibleOutlet->name);
        $response->assertDontSee($hiddenOutlet->name);
    }

    public function test_admin_can_search_outlets_via_ajax_endpoint(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN_PUSAT,
            'branch_id' => null,
        ]);

        $outlet = Outlet::factory()->create([
            'name' => 'Salon Mawar Jaya',
        ]);

        $response = $this->actingAs($admin)->getJson(route('ajax.outlets.search', ['q' => 'Mawar']));

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => $outlet->name,
            ]);
    }

    public function test_pelanggan_lama_requires_official_kode(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);

        $response = $this->actingAs($sales)->post(route('outlets.store'), [
            'name' => 'Outlet Baru',
            'address' => 'Jalan Test',
            'district' => 'Coblong',
            'city' => 'Bandung',
            'category' => 'toko',
            'outlet_status' => 'active',
        ]);

        $response->assertSessionHasErrors('official_kode');
    }

    public function test_supervisor_can_create_prospect_outlet_without_official_kode(): void
    {
        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $response = $this->actingAs($supervisor)->post(route('outlets.store'), [
            'name' => 'Outlet Prospek',
            'address' => 'Jalan Prospek',
            'district' => 'Cidadap',
            'city' => 'Bandung',
            'category' => 'toko',
            'outlet_status' => 'prospek',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('outlets', [
            'name' => 'Outlet Prospek',
            'outlet_status' => 'prospek',
        ]);
    }

    public function test_sales_cannot_edit_outlet_master(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);
        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $sales->id,
        ]);

        $this->actingAs($sales)
            ->get(route('outlets.edit', $outlet))
            ->assertForbidden();
    }

    public function test_smd_cannot_edit_outlet_master(): void
    {
        $branch = Branch::factory()->create();
        $smd = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SMD,
        ]);
        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $smd->id,
        ]);

        $this->actingAs($smd)
            ->get(route('outlets.edit', $outlet))
            ->assertForbidden();
    }

    public function test_sales_can_access_prospect_follow_up_list(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);
        $prospect = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $sales->id,
            'name' => 'Prospek Follow Up',
            'outlet_status' => 'prospek',
        ]);

        $this->actingAs($sales)
            ->get(route('outlet-lists.prospects'))
            ->assertOk()
            ->assertSee($prospect->name);
    }

    public function test_sales_cannot_access_noo_operational_list(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);

        $this->actingAs($sales)
            ->get(route('outlet-lists.noo'))
            ->assertForbidden();
    }
}
