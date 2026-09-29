<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('message')->nullable();
            $table->string('source')->default('site');

            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('stage', [
                'novo', 'em_contato', 'visita_agendada', 'proposta', 'fechado_ganho', 'fechado_perdido',
            ])->default('novo');
            $table->string('lost_reason')->nullable();

            $table->decimal('negotiated_value', 12, 2)->nullable();
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->decimal('commission_value', 12, 2)->nullable();
            $table->boolean('commission_paid')->default(false);
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();

            $table->index('stage');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
