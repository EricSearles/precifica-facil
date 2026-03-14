<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('provider');
            $table->string('event_id')->nullable();
            $table->string('event_type')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('resource_id')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->string('provider_status')->nullable();
            $table->string('local_status')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->longText('payload');
            $table->longText('response_payload')->nullable();
            $table->timestamps();

            $table->index(['provider', 'resource_id']);
            $table->unique(['provider', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_webhook_events');
    }
};
