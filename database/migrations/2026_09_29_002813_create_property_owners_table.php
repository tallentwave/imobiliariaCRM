<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->decimal('ownership_percentage', 5, 2)->nullable();
            $table->boolean('primary_contact')->default(false);
            $table->enum('authorization_status', ['PENDING', 'AUTHORIZED', 'REVOKED'])->default('PENDING');
            $table->timestamps();

            $table->unique(['property_id', 'contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_owners');
    }
};
