<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['BUYER', 'SELLER', 'BUYER_REPRESENTATIVE', 'SELLER_REPRESENTATIVE', 'WITNESS', 'OTHER'])->default('OTHER');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_parties');
    }
};
