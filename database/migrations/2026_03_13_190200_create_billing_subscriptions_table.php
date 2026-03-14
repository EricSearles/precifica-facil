<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('billing_customer_id')->nullable()->constrained('billing_customers')->nullOnDelete();
            $table->foreignId('billing_plan_id')->nullable()->constrained('billing_plans')->nullOnDelete();
            $table->string('provider')->default('asaas');
            $table->string('provider_subscription_id')->nullable()->index();
            $table->string('status')->default('pending');
            $table->string('billing_method', 30)->nullable();
            $table->unsignedBigInteger('amount_cents')->default(0);
            $table->string('currency', 3)->default('BRL');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('next_due_at')->nullable();
            $table->timestamp('canceled_at')->nullable();
            $table->longText('metadata')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_subscription_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_subscriptions');
    }
};
