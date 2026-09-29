<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('creci')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('creci');
            $table->boolean('active')->default(true)->after('avatar');
            $table->decimal('default_commission_percent', 5, 2)->default(0)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'creci', 'avatar', 'active', 'default_commission_percent']);
        });
    }
};
