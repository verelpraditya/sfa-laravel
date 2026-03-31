<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\SalesVisitDetail;
use App\Models\SmdVisitActivity;
use App\Models\SmdVisitDetail;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $branch = Branch::firstOrCreate(
            ['code' => 'BDG'],
            [
                'name' => 'Cabang Bandung',
                'city' => 'Bandung',
                'timezone' => 'Asia/Jakarta',
                'address' => 'Bandung',
                'is_active' => true,
            ],
        );

        User::updateOrCreate([
            'username' => 'adminpusat',
        ], [
            'name' => 'Admin Pusat',
            'email' => 'admin@sfa.test',
            'branch_id' => null,
            'role' => User::ROLE_ADMIN_PUSAT,
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'username' => 'supervisorbdg',
        ], [
            'name' => 'Supervisor Bandung',
            'email' => 'supervisor.bdg@sfa.test',
            'branch_id' => $branch->id,
            'role' => User::ROLE_SUPERVISOR,
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'username' => 'salesbdg',
        ], [
            'name' => 'Sales Bandung',
            'email' => 'sales.bdg@sfa.test',
            'branch_id' => $branch->id,
            'role' => User::ROLE_SALES,
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        User::updateOrCreate([
            'username' => 'smdbdg',
        ], [
            'name' => 'SMD Bandung',
            'email' => 'smd.bdg@sfa.test',
            'branch_id' => $branch->id,
            'role' => User::ROLE_SMD,
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);

        $creator = User::where('username', 'supervisorbdg')->first();

        foreach ([
            [
                'name' => 'Salon Mawar Jaya',
                'district' => 'Coblong',
                'city' => 'Bandung',
                'category' => 'salon',
                'outlet_status' => 'active',
                'official_kode' => 'OFF-BDG-001',
            ],
            [
                'name' => 'Toko Sinar Baru',
                'district' => 'Sukajadi',
                'city' => 'Bandung',
                'category' => 'toko',
                'outlet_status' => 'prospek',
                'official_kode' => null,
            ],
            [
                'name' => 'Barbershop Central',
                'district' => 'Lengkong',
                'city' => 'Bandung',
                'category' => 'barbershop',
                'outlet_status' => 'pending',
                'official_kode' => null,
            ],
        ] as $outletData) {
            Outlet::updateOrCreate([
                'branch_id' => $branch->id,
                'name' => $outletData['name'],
            ], [
                'address' => 'Bandung',
                'district' => $outletData['district'],
                'city' => $outletData['city'],
                'category' => $outletData['category'],
                'outlet_status' => $outletData['outlet_status'],
                'official_kode' => $outletData['official_kode'],
                'verified_by' => $outletData['outlet_status'] === 'active' ? $creator?->id : null,
                'verified_at' => $outletData['outlet_status'] === 'active' ? now() : null,
                'created_by' => $creator?->id,
                'updated_by' => $creator?->id,
            ]);
        }

        $sales = User::where('username', 'salesbdg')->first();
        $mawar = Outlet::where('name', 'Salon Mawar Jaya')->first();

        if ($sales && $mawar) {
            $visit = Visit::updateOrCreate([
                'branch_id' => $branch->id,
                'outlet_id' => $mawar->id,
                'user_id' => $sales->id,
                'visit_type' => 'sales',
                'visited_at' => now()->subDay(),
            ], [
                'outlet_condition' => 'buka',
                'latitude' => -6.9175000,
                'longitude' => 107.6191000,
                'visit_photo_path' => 'seed/visits/sales-1.jpg',
                'notes' => 'Seed visit untuk dashboard awal.',
            ]);

            SalesVisitDetail::updateOrCreate([
                'visit_id' => $visit->id,
            ], [
                'order_amount' => 250000,
                'receivable_amount' => 175000,
            ]);
        }

        $smd = User::where('username', 'smdbdg')->first();
        $sinar = Outlet::where('name', 'Toko Sinar Baru')->first();

        if ($smd && $sinar) {
            $visit = Visit::updateOrCreate([
                'branch_id' => $branch->id,
                'outlet_id' => $sinar->id,
                'user_id' => $smd->id,
                'visit_type' => 'smd',
                'visited_at' => now()->subHours(6),
            ], [
                'outlet_condition' => null,
                'latitude' => -6.9175000,
                'longitude' => 107.6191000,
                'visit_photo_path' => 'seed/visits/smd-1.jpg',
                'notes' => 'Seed visit SMD awal.',
            ]);

            SmdVisitDetail::updateOrCreate([
                'visit_id' => $visit->id,
            ], [
                'po_amount' => 180000,
                'payment_amount' => null,
                'display_photo_path' => 'seed/visits/display-1.jpg',
            ]);

            foreach (['ambil_po', 'merapikan_display'] as $activity) {
                SmdVisitActivity::updateOrCreate([
                    'visit_id' => $visit->id,
                    'activity_type' => $activity,
                ]);
            }
        }
    }
}
