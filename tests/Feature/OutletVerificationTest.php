<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutletVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_outlet_becomes_active_when_official_kode_is_filled(): void
    {
        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $supervisor->id,
            'outlet_status' => 'pending',
            'official_kode' => null,
        ]);

        $response = $this->actingAs($supervisor)->put(route('outlet-verifications.update', $outlet), [
            'category' => 'toko',
            'outlet_status' => 'active',
            'official_kode' => 'OFF-NEW-001',
            'verification_notes' => 'Kode resmi sudah masuk.',
        ]);

        $response->assertRedirect(route('outlet-verifications.edit', $outlet));
        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'outlet_status' => 'active',
            'official_kode' => 'OFF-NEW-001',
        ]);
        $this->assertDatabaseHas('outlet_verification_logs', [
            'outlet_id' => $outlet->id,
            'official_kode' => 'OFF-NEW-001',
        ]);
    }

    public function test_supervisor_can_mark_outlet_as_inactive(): void
    {
        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $supervisor->id,
            'official_kode' => 'OFF-OLD-001',
            'outlet_status' => 'active',
        ]);

        $response = $this->actingAs($supervisor)->put(route('outlet-verifications.update', $outlet), [
            'category' => 'toko',
            'outlet_status' => 'inactive',
            'official_kode' => 'OFF-OLD-001',
        ]);

        $response->assertRedirect(route('outlet-verifications.edit', $outlet));
        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'outlet_status' => 'inactive',
        ]);
    }

    public function test_sales_cannot_access_verification_routes(): void
    {
        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);

        $response = $this->actingAs($sales)->get(route('outlet-verifications.index'));

        $response->assertForbidden();
    }
}
