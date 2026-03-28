<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('address');
            $table->string('district');
            $table->string('city');
            $table->enum('category', ['salon', 'toko', 'barbershop', 'lainnya']);
            $table->enum('outlet_type', ['prospek', 'noo', 'pelanggan_lama']);
            $table->enum('outlet_status', ['active', 'inactive'])->default('active');
            $table->string('official_kode')->nullable()->unique();
            $table->enum('verification_status', ['pending', 'verified'])->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['branch_id', 'name']);
            $table->index(['branch_id', 'outlet_type']);
            $table->index(['branch_id', 'outlet_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
