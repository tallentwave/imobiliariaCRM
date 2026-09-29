<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->foreignId('opportunity_id')->nullable()->after('lead_id')->constrained()->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->after('opportunity_id')->constrained()->nullOnDelete();
            $table->string('key_control_code')->nullable();
            $table->enum('feedback_intent', ['MUITO_INTERESSADO', 'INTERESSADO', 'NEUTRO', 'SEM_INTERESSE'])->nullable();
            $table->text('feedback_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('opportunity_id');
            $table->dropConstrainedForeignId('contact_id');
            $table->dropColumn(['key_control_code', 'feedback_intent', 'feedback_notes']);
        });
    }
};
