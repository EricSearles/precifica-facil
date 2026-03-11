<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();

            $table->string('name');
            $table->string('sale_unit', 20)->default('un'); // un, kg, cento etc.

            $table->decimal('yield_quantity', 12, 3)->default(1);

            $table->enum('profit_margin_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('profit_margin_value', 12, 2)->default(0);
            $table->boolean('use_global_margin')->default(false);

            $table->decimal('calculated_unit_cost', 12, 4)->default(0);
            $table->decimal('suggested_sale_price', 12, 4)->default(0);

            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};