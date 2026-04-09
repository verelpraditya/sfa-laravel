<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE visits MODIFY outlet_condition ENUM('buka','tutup','order_by_wa') NULL");
    }

    public function down(): void
    {
        DB::table('visits')
            ->where('outlet_condition', 'order_by_wa')
            ->update(['outlet_condition' => 'buka']);

        DB::statement("ALTER TABLE visits MODIFY outlet_condition ENUM('buka','tutup') NULL");
    }
};
