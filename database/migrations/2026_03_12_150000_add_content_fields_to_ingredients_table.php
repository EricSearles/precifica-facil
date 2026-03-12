<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->decimal('content_quantity', 12, 3)->nullable()->after('purchase_price');
            $table->string('content_unit', 20)->nullable()->after('content_quantity');
        });
    }

    public function down(): void
    {
        Schema::table('ingredients', function (Blueprint $table) {
            $table->dropColumn(['content_quantity', 'content_unit']);
        });
    }
};
