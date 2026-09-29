<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_plan_id')->constrained()->cascadeOnDelete();
            $table->enum('dimension', ['CAP', 'BUY', 'REF', 'CORP', 'TEAM', 'LEADER', 'UNIT', 'COMPANY', 'PARTNER']);
            $table->decimal('percentage', 5, 2);
            $table->text('conditions')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_rules');
    }
};
