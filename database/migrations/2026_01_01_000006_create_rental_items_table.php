<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rental_items')) {
            return;
        }

        Schema::create('rental_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained()->restrictOnDelete();
            $table->decimal('valor_diaria', 10, 2);
            $table->timestamps();
            $table->unique(['rental_id', 'equipment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_items');
    }
};
