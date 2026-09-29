<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->foreignId('parent_proposal_id')->nullable()->constrained('proposals')->nullOnDelete();
            $table->unsignedInteger('version')->default(1);

            $table->decimal('price', 12, 2);
            $table->decimal('down_payment', 12, 2)->nullable();
            $table->decimal('financing_amount', 12, 2)->nullable();
            $table->decimal('fgts_amount', 12, 2)->nullable();
            $table->text('trade_in_notes')->nullable();
            $table->text('conditions')->nullable();
            $table->dateTime('valid_until')->nullable();

            $table->enum('status', ['DRAFT', 'PRESENTED', 'COUNTERED', 'ACCEPTED', 'REJECTED', 'EXPIRED'])->default('DRAFT');

            $table->timestamps();

            $table->index(['property_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
