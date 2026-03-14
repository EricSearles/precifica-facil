<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('mobile_phone', 30)->nullable()->after('phone');
        });

        Schema::table('billing_customers', function (Blueprint $table) {
            $table->string('mobile_phone', 30)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('billing_customers', function (Blueprint $table) {
            $table->dropColumn('mobile_phone');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('mobile_phone');
        });
    }
};
