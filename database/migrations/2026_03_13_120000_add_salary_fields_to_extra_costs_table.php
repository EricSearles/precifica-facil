<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extra_costs', function (Blueprint $table) {
            $table->decimal('monthly_salary', 12, 4)->nullable()->after('labor_hourly_rate');
            $table->unsignedInteger('monthly_hours')->nullable()->after('monthly_salary');
        });
    }

    public function down(): void
    {
        Schema::table('extra_costs', function (Blueprint $table) {
            $table->dropColumn(['monthly_salary', 'monthly_hours']);
        });
    }
};
