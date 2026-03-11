<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();

            $table->string('name');
            $table->decimal('yield_quantity', 12, 3)->default(1);
            $table->string('yield_unit', 20)->default('un');

            $table->decimal('ingredients_cost_total', 12, 4)->default(0);
            $table->decimal('extra_cost_total', 12, 4)->default(0);
            $table->decimal('packaging_cost_total', 12, 4)->default(0);
            $table->decimal('recipe_total_cost', 12, 4)->default(0);
            $table->decimal('unit_cost', 12, 4)->default(0);
            $table->decimal('suggested_sale_price', 12, 4)->default(0);

            $table->text('preparation_method')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};