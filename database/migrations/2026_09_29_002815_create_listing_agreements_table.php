<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listing_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('captor_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('listing_type', ['OPEN', 'EXCLUSIVE', 'SIGNATURE'])->default('OPEN');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->decimal('commission_fixed', 12, 2)->nullable();
            $table->json('authorizations')->nullable();

            $table->enum('status', [
                'PROSPECT', 'CONTACTED', 'MEETING', 'VALUATION', 'PROPOSAL',
                'CONTRACT', 'ONBOARDING', 'ACTIVE', 'EXPIRED', 'CANCELLED',
            ])->default('PROSPECT');

            $table->string('signed_document_path')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_agreements');
    }
};
