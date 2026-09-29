<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commission_plan_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('gross_value', 12, 2);
            $table->decimal('total_commission_percent', 5, 2);
            $table->decimal('total_commission_value', 12, 2);

            $table->enum('status', ['PENDING', 'APPROVED', 'PAID', 'CANCELLED'])->default('PENDING');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_events');
    }
};
