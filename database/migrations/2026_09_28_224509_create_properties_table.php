<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->enum('purpose', ['venda', 'aluguel', 'temporada'])->default('venda');
            $table->enum('type', [
                'apartamento', 'casa', 'casa_condominio', 'cobertura', 'terreno',
                'comercial', 'sala', 'galpao', 'rural', 'outro',
            ])->default('apartamento');

            $table->decimal('price', 12, 2)->nullable();
            $table->decimal('condo_fee', 10, 2)->nullable();
            $table->decimal('iptu', 10, 2)->nullable();

            $table->unsignedSmallInteger('bedrooms')->default(0);
            $table->unsignedSmallInteger('suites')->default(0);
            $table->unsignedSmallInteger('bathrooms')->default(0);
            $table->unsignedSmallInteger('parking_spots')->default(0);
            $table->decimal('area_total', 10, 2)->nullable();
            $table->decimal('area_built', 10, 2)->nullable();

            $table->string('zipcode', 9)->nullable();
            $table->string('address')->nullable();
            $table->string('number', 20)->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 2)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('show_exact_address')->default(true);

            $table->enum('status', ['disponivel', 'reservado', 'vendido', 'alugado', 'inativo'])
                ->default('disponivel');
            $table->boolean('featured')->default(false);
            $table->string('reference_code')->nullable()->unique();
            $table->unsignedInteger('views_count')->default(0);

            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['purpose', 'type', 'status']);
            $table->index(['city', 'neighborhood']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
