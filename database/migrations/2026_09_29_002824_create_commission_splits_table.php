<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('dimension', ['CAP', 'BUY', 'REF', 'CORP', 'TEAM', 'LEADER', 'UNIT', 'COMPANY', 'PARTNER']);
            $table->decimal('percentage', 5, 2);
            $table->decimal('value', 12, 2);
            $table->boolean('paid')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_splits');
    }
};
