<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('related_contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->enum('relationship_type', ['CONJUGE', 'SOCIO', 'PROCURADOR', 'REPRESENTANTE', 'INDICADOR', 'OUTRO']);
            $table->timestamps();

            $table->unique(['contact_id', 'related_contact_id', 'relationship_type'], 'contact_rel_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_relationships');
    }
};
