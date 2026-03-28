<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('outlet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('visit_type', ['sales', 'smd']);
            $table->enum('outlet_condition', ['buka', 'tutup'])->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('visit_photo_path');
            $table->timestamp('visited_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['branch_id', 'visited_at']);
            $table->index(['user_id', 'visited_at']);
            $table->index(['outlet_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
