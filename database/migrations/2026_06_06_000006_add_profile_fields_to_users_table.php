<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('role');
            $table->string('company')->nullable()->after('phone');
            $table->string('designation')->nullable()->after('company');
            $table->string('country')->nullable()->after('designation');
            $table->boolean('is_active')->default(true)->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'company', 'designation', 'country', 'is_active']);
        });
    }
};
