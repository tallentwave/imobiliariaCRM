<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['stage']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['stage', 'negotiated_value', 'commission_percent', 'commission_value', 'commission_paid', 'closed_at']);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('unit_id')->nullable()->after('organization_id')->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->after('unit_id')->constrained()->nullOnDelete();
            $table->foreignId('assigned_team_id')->nullable()->after('agent_id')->constrained('teams')->nullOnDelete();

            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();

            $table->enum('temperature', ['COLD', 'WARM', 'HOT'])->default('WARM');
            $table->unsignedTinyInteger('lead_score')->default(0);

            $table->enum('stage', [
                'NEW', 'ATTEMPTING_CONTACT', 'CONTACTED', 'QUALIFYING', 'QUALIFIED', 'OPPORTUNITY',
                'NURTURE', 'LOST', 'SPAM', 'DUPLICATE', 'INVALID',
            ])->default('NEW');

            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('next_activity_at')->nullable();
            $table->timestamp('sla_due_at')->nullable();
            $table->timestamp('escalated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organization_id');
            $table->dropConstrainedForeignId('unit_id');
            $table->dropConstrainedForeignId('contact_id');
            $table->dropConstrainedForeignId('assigned_team_id');
            $table->dropColumn([
                'utm_source', 'utm_medium', 'utm_campaign', 'temperature', 'lead_score',
                'stage', 'lost_reason', 'first_response_at', 'last_activity_at',
                'next_activity_at', 'sla_due_at', 'escalated_at',
            ]);
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->enum('stage', ['novo', 'em_contato', 'visita_agendada', 'proposta', 'fechado_ganho', 'fechado_perdido'])->default('novo');
            $table->decimal('negotiated_value', 12, 2)->nullable();
            $table->decimal('commission_percent', 5, 2)->nullable();
            $table->decimal('commission_value', 12, 2)->nullable();
            $table->boolean('commission_paid')->default(false);
            $table->timestamp('closed_at')->nullable();
        });
    }
};
