<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SmdVisitTest extends TestCase
{
    use RefreshDatabase;

    public function test_smd_can_create_visit_with_multiple_activities(): void
    {
        Storage::fake('public');

        $branch = Branch::factory()->create();
        $smd = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SMD,
        ]);
        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $smd->id,
        ]);

        $response = $this->actingAs($smd)->post(route('smd-visits.store'), [
            'outlet_id' => $outlet->id,
            'activities' => ['ambil_po', 'merapikan_display'],
            'po_amount' => 120000,
            'display_photos' => [
                UploadedFile::fake()->image('display-1.jpg'),
                UploadedFile::fake()->image('display-2.jpg'),
            ],
            'latitude' => '-6.9175000',
            'longitude' => '107.6191000',
            'visit_photo' => UploadedFile::fake()->image('visit.jpg'),
        ]);

        $response->assertRedirect(route('smd-visits.index'));
        $this->assertDatabaseHas('visits', [
            'outlet_id' => $outlet->id,
            'user_id' => $smd->id,
            'visit_type' => 'smd',
        ]);
        $this->assertDatabaseHas('smd_visit_details', [
            'po_amount' => 120000,
        ]);
        $this->assertDatabaseHas('smd_visit_activities', [
            'activity_type' => 'ambil_po',
        ]);
        $this->assertDatabaseHas('smd_visit_activities', [
            'activity_type' => 'merapikan_display',
        ]);
        $this->assertDatabaseCount('smd_visit_display_photos', 2);
    }

    public function test_ambil_tagihan_requires_payment_amount(): void
    {
        Storage::fake('public');

        $branch = Branch::factory()->create();
        $smd = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SMD,
        ]);
        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $smd->id,
        ]);

        $response = $this->actingAs($smd)->from(route('smd-visits.create'))->post(route('smd-visits.store'), [
            'outlet_id' => $outlet->id,
            'activities' => ['ambil_tagihan'],
            'latitude' => '-6.9175000',
            'longitude' => '107.6191000',
            'visit_photo' => UploadedFile::fake()->image('visit.jpg'),
        ]);

        $response->assertRedirect(route('smd-visits.create'));
        $response->assertSessionHasErrors('payment_amount');
    }

    public function test_merapikan_display_requires_display_photo(): void
    {
        Storage::fake('public');

        $branch = Branch::factory()->create();
        $supervisor = User::factory()->create([
            'branch_id' => $branch->id,
            'role' => User::ROLE_SUPERVISOR,
        ]);
        $outlet = Outlet::factory()->create([
            'branch_id' => $branch->id,
            'created_by' => $supervisor->id,
        ]);

        $response = $this->actingAs($supervisor)->from(route('smd-visits.create'))->post(route('smd-visits.store'), [
            'outlet_id' => $outlet->id,
            'activities' => ['merapikan_display'],
            'latitude' => '-6.9175000',
            'longitude' => '107.6191000',
            'visit_photo' => UploadedFile::fake()->image('visit.jpg'),
        ]);

        $response->assertRedirect(route('smd-visits.create'));
        $response->assertSessionHasErrors('display_photos');
    }
}
