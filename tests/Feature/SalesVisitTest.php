<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SalesVisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_can_create_visit_with_existing_outlet(): void
    {
        Storage::fake('public');

        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);
        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $sales->id,
        ]);

        $response = $this->actingAs($sales)->post(route('sales-visits.store'), [
            'outlet_id' => $outlet->id,
            'outlet_condition' => 'buka',
            'order_amount' => 150000,
            'receivable_amount' => 200000,
            'latitude' => '-6.9175000',
            'longitude' => '107.6191000',
            'visit_photo' => UploadedFile::fake()->image('visit.jpg'),
            'notes' => 'Outlet ramai saat kunjungan.',
        ]);

        $response->assertRedirect(route('sales-visits.index'));
        $this->assertDatabaseHas('visits', [
            'outlet_id' => $outlet->id,
            'user_id' => $sales->id,
            'visit_type' => 'sales',
            'outlet_condition' => 'buka',
        ]);
        $this->assertDatabaseHas('sales_visit_details', [
            'order_amount' => 150000,
            'receivable_amount' => 200000,
        ]);
    }

    public function test_sales_can_create_visit_with_new_outlet_inline(): void
    {
        Storage::fake('public');

        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);

        $response = $this->actingAs($sales)->post(route('sales-visits.store'), [
            'new_outlet_name' => 'Outlet Prospek Baru',
            'new_outlet_address' => 'Jalan Baru No. 10',
            'new_outlet_district' => 'Coblong',
            'new_outlet_city' => 'Bandung',
            'new_outlet_category' => 'toko',
            'new_outlet_type' => 'prospek',
            'outlet_condition' => 'tutup',
            'latitude' => '-6.9175000',
            'longitude' => '107.6191000',
            'visit_photo' => UploadedFile::fake()->image('visit.jpg'),
        ]);

        $response->assertRedirect(route('sales-visits.index'));
        $this->assertDatabaseHas('outlets', [
            'branch_id' => $branch->id,
            'name' => 'Outlet Prospek Baru',
            'outlet_status' => 'prospek',
        ]);
        $this->assertDatabaseHas('visits', [
            'visit_type' => 'sales',
            'outlet_condition' => 'tutup',
        ]);
    }

    public function test_sales_cannot_submit_transaction_values_when_outlet_is_closed(): void
    {
        Storage::fake('public');

        $branch = Branch::factory()->create();
        $sales = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
        ]);
        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $sales->id,
        ]);

        $response = $this->actingAs($sales)->from(route('sales-visits.create'))->post(route('sales-visits.store'), [
            'outlet_id' => $outlet->id,
            'outlet_condition' => 'tutup',
            'order_amount' => 1000,
            'latitude' => '-6.9175000',
            'longitude' => '107.6191000',
            'visit_photo' => UploadedFile::fake()->image('visit.jpg'),
        ]);

        $response->assertRedirect(route('sales-visits.create'));
        $response->assertSessionHasErrors('order_amount');
    }
}
