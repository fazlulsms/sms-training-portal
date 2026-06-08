<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'category')) {
                $table->string('category')->nullable()->after('code');
            }

            if (!Schema::hasColumn('courses', 'duration')) {
                $table->string('duration')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'duration')) {
                $table->dropColumn('duration');
            }

            if (Schema::hasColumn('courses', 'category')) {
                $table->dropColumn('category');
            }
        });
    }
};