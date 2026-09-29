<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->json('sla_milestones_notified')->nullable()->after('escalated_at');
            $table->timestamp('redistributed_at')->nullable()->after('sla_milestones_notified');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['sla_milestones_notified', 'redistributed_at']);
        });
    }
};
