<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smd_visit_display_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['visit_id', 'sort_order']);
        });

        DB::table('smd_visit_details')
            ->whereNotNull('display_photo_path')
            ->orderBy('visit_id')
            ->lazy()
            ->each(function ($detail): void {
                DB::table('smd_visit_display_photos')->insert([
                    'visit_id' => $detail->visit_id,
                    'photo_path' => $detail->display_photo_path,
                    'sort_order' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('smd_visit_display_photos');
    }
};
