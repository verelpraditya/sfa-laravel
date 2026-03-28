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

    public function test_noo_becomes_verified_automatically_when_changed_to_pelanggan_lama_with_official_kode(): void
    {
        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SUPERVISOR,
        ]);

        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $supervisor->id,
            'outlet_type' => 'noo',
            'verification_status' => 'pending',
            'official_kode' => null,
        ]);

        $response = $this->actingAs($supervisor)->put(route('outlet-verifications.update', $outlet), [
            'category' => 'toko',
            'outlet_type' => 'pelanggan_lama',
            'outlet_status' => 'active',
            'official_kode' => 'OFF-NEW-001',
            'verification_status' => 'pending',
            'verification_notes' => 'Kode resmi sudah masuk.',
        ]);

        $response->assertRedirect(route('outlet-verifications.edit', $outlet));
        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'outlet_type' => 'pelanggan_lama',
            'official_kode' => 'OFF-NEW-001',
            'verification_status' => 'verified',
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
            'outlet_type' => 'pelanggan_lama',
            'official_kode' => 'OFF-OLD-001',
            'verification_status' => 'verified',
            'outlet_status' => 'active',
        ]);

        $response = $this->actingAs($supervisor)->put(route('outlet-verifications.update', $outlet), [
            'category' => 'toko',
            'outlet_type' => 'pelanggan_lama',
            'outlet_status' => 'inactive',
            'official_kode' => 'OFF-OLD-001',
            'verification_status' => 'verified',
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
