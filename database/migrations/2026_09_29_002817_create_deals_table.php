<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('opportunity_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('proposal_id')->nullable()->constrained()->nullOnDelete();

            $table->foreignId('buyer_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('seller_contact_id')->nullable()->constrained('contacts')->nullOnDelete();

            $table->foreignId('agent_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('captor_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->decimal('value', 12, 2);
            $table->enum('status', [
                'NEGOTIATION', 'CONTRACT', 'DOCUMENTATION', 'CLOSED_WON', 'CLOSED_LOST',
            ])->default('NEGOTIATION');
            $table->string('lost_reason')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
