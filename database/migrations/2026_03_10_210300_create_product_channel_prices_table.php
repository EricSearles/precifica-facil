<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_channel_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('sales_channel_id')->constrained('sales_channels')->cascadeOnDelete();
            $table->decimal('reference_price', 12, 2)->default(0);
            $table->decimal('desired_net_value', 12, 2)->default(0);
            $table->decimal('percentage_fee_total', 12, 2)->default(0);
            $table->decimal('fixed_fee_total', 12, 2)->default(0);
            $table->decimal('fee_total', 12, 2)->default(0);
            $table->decimal('channel_price', 12, 2)->default(0);
            $table->decimal('net_value', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'sales_channel_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_channel_prices');
    }
};
