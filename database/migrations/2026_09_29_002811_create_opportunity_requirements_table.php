<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunity_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opportunity_id')->constrained()->cascadeOnDelete();
            $table->string('feature_key');
            $table->string('feature_value');
            $table->enum('priority', ['OBRIGATORIO', 'DESEJAVEL', 'INDIFERENTE', 'EXCLUDENTE'])->default('DESEJAVEL');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunity_requirements');
    }
};
