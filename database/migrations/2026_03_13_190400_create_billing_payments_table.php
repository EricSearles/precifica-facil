<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('billing_customer_id')->nullable()->constrained('billing_customers')->nullOnDelete();
            $table->foreignId('billing_invoice_id')->nullable()->constrained('billing_invoices')->nullOnDelete();
            $table->string('provider')->default('asaas');
            $table->string('provider_payment_id')->nullable()->index();
            $table->string('status')->default('pending');
            $table->string('method', 30)->nullable();
            $table->unsignedBigInteger('amount_cents')->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->longText('raw_payload')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->unique(['provider', 'provider_payment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_payments');
    }
};
