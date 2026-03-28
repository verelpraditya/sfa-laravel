<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\User;
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
                'outlet_type' => 'pelanggan_lama',
                'official_kode' => 'OFF-BDG-001',
                'verification_status' => 'verified',
            ],
            [
                'name' => 'Toko Sinar Baru',
                'district' => 'Sukajadi',
                'city' => 'Bandung',
                'category' => 'toko',
                'outlet_type' => 'prospek',
                'official_kode' => null,
                'verification_status' => 'pending',
            ],
            [
                'name' => 'Barbershop Central',
                'district' => 'Lengkong',
                'city' => 'Bandung',
                'category' => 'barbershop',
                'outlet_type' => 'noo',
                'official_kode' => null,
                'verification_status' => 'pending',
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
                'outlet_type' => $outletData['outlet_type'],
                'official_kode' => $outletData['official_kode'],
                'verification_status' => $outletData['verification_status'],
                'verified_by' => $outletData['verification_status'] === 'verified' ? $creator?->id : null,
                'verified_at' => $outletData['verification_status'] === 'verified' ? now() : null,
                'created_by' => $creator?->id,
                'updated_by' => $creator?->id,
            ]);
        }
    }
}
