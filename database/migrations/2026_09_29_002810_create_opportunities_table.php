<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('purpose', ['MORAR', 'INVESTIR', 'RENDA', 'COMERCIAL'])->default('MORAR');
            $table->decimal('budget_min', 12, 2)->nullable();
            $table->decimal('budget_max', 12, 2)->nullable();
            $table->string('urgency')->nullable();
            $table->date('timeline')->nullable();
            $table->boolean('financing_required')->default(false);
            $table->decimal('down_payment', 12, 2)->nullable();
            $table->boolean('fgts')->default(false);
            $table->boolean('trade_in')->default(false);
            $table->text('trade_in_notes')->nullable();

            $table->enum('status', ['OPEN', 'WON', 'LOST'])->default('OPEN');
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunities');
    }
};
