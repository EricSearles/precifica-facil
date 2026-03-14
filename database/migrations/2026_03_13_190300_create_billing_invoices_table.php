<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('billing_customer_id')->nullable()->constrained('billing_customers')->nullOnDelete();
            $table->foreignId('billing_subscription_id')->nullable()->constrained('billing_subscriptions')->nullOnDelete();
            $table->string('provider')->default('asaas');
            $table->string('provider_invoice_id')->nullable()->index();
            $table->string('number')->nullable();
            $table->string('status')->default('pending');
            $table->string('billing_method', 30)->nullable();
            $table->string('currency', 3)->default('BRL');
            $table->unsignedBigInteger('amount_cents')->default(0);
            $table->unsignedBigInteger('discount_cents')->default(0);
            $table->unsignedBigInteger('total_cents')->default(0);
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->string('provider_status')->nullable();
            $table->string('invoice_url')->nullable();
            $table->string('boleto_line')->nullable();
            $table->string('boleto_pdf_url')->nullable();
            $table->text('pix_qr_code')->nullable();
            $table->text('pix_copy_paste')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->string('cancel_reason')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamps();

            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'due_at']);
            $table->unique(['provider', 'provider_invoice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_invoices');
    }
};
