<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE outlets MODIFY outlet_status ENUM('prospek','pending','active','inactive') NOT NULL DEFAULT 'prospek'");

        DB::table('outlets')
            ->where('outlet_status', 'inactive')
            ->update(['outlet_status' => 'inactive']);

        DB::table('outlets')
            ->where('outlet_status', '!=', 'inactive')
            ->whereNotNull('official_kode')
            ->update(['outlet_status' => 'active']);

        DB::table('outlets')
            ->where('outlet_status', '!=', 'inactive')
            ->whereNull('official_kode')
            ->where('outlet_type', 'pelanggan_lama')
            ->update(['outlet_status' => 'active']);

        DB::table('outlets')
            ->where('outlet_status', '!=', 'inactive')
            ->where('outlet_type', 'noo')
            ->whereNull('official_kode')
            ->update(['outlet_status' => 'pending']);

        DB::table('outlets')
            ->where('outlet_status', '!=', 'inactive')
            ->where('outlet_type', 'prospek')
            ->update(['outlet_status' => 'prospek']);

        Schema::table('outlets', function (Blueprint $table): void {
            $table->dropColumn(['outlet_type', 'verification_status']);
        });
    }

    public function down(): void
    {
        Schema::table('outlets', function (Blueprint $table): void {
            $table->enum('outlet_type', ['prospek', 'noo', 'pelanggan_lama'])->default('prospek')->after('category');
            $table->enum('verification_status', ['pending', 'verified'])->nullable()->after('official_kode');
        });

        DB::table('outlets')
            ->where('outlet_status', 'prospek')
            ->update([
                'outlet_type' => 'prospek',
                'verification_status' => null,
            ]);

        DB::table('outlets')
            ->where('outlet_status', 'pending')
            ->update([
                'outlet_type' => 'noo',
                'verification_status' => 'pending',
            ]);

        DB::table('outlets')
            ->where('outlet_status', 'active')
            ->update([
                'outlet_type' => DB::raw("CASE WHEN official_kode IS NULL OR official_kode = '' THEN 'pelanggan_lama' ELSE 'pelanggan_lama' END"),
                'verification_status' => DB::raw("CASE WHEN official_kode IS NULL OR official_kode = '' THEN NULL ELSE 'verified' END"),
            ]);

        DB::table('outlets')
            ->where('outlet_status', 'inactive')
            ->update([
                'outlet_type' => DB::raw("CASE WHEN official_kode IS NULL OR official_kode = '' THEN 'noo' ELSE 'pelanggan_lama' END"),
                'verification_status' => DB::raw("CASE WHEN official_kode IS NULL OR official_kode = '' THEN 'pending' ELSE 'verified' END"),
            ]);

        DB::statement("ALTER TABLE outlets MODIFY outlet_status ENUM('active','inactive') NOT NULL DEFAULT 'active'");
    }
};
