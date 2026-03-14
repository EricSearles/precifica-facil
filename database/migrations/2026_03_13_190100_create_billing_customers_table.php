<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('provider')->default('asaas');
            $table->string('provider_customer_id')->nullable()->index();
            $table->string('name');
            $table->string('document_type', 20)->nullable();
            $table->string('document', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('billing_email')->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['provider', 'provider_customer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_customers');
    }
};
