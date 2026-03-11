<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extra_costs', function (Blueprint $table) {
            $table->unsignedInteger('labor_minutes')->nullable()->after('value');
            $table->decimal('labor_hourly_rate', 12, 4)->nullable()->after('labor_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('extra_costs', function (Blueprint $table) {
            $table->dropColumn(['labor_minutes', 'labor_hourly_rate']);
        });
    }
};
