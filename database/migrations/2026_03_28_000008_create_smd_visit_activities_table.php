<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smd_visit_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->cascadeOnDelete();
            $table->enum('activity_type', ['ambil_po', 'merapikan_display', 'tukar_faktur', 'ambil_tagihan']);
            $table->timestamps();

            $table->index(['visit_id', 'activity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smd_visit_activities');
    }
};
