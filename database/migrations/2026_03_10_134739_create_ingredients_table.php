<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            $table->string('name');
            $table->string('brand')->nullable();

            $table->string('purchase_unit', 20); // kg, g, l, ml, un
            $table->decimal('purchase_quantity', 12, 3);
            $table->decimal('purchase_price', 12, 2);

            $table->string('base_unit', 20)->nullable(); // g, ml, un
            $table->decimal('base_quantity', 12, 3)->nullable();

            $table->decimal('unit_cost', 12, 6)->default(0);

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredients');
    }
};