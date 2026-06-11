<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('training_schedules', 'city')) {
                $table->string('city')->nullable()->after('venue');
            }
            if (!Schema::hasColumn('training_schedules', 'country')) {
                $table->string('country')->nullable()->after('city');
            }
            if (!Schema::hasColumn('training_schedules', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('is_public');
            }
        });
    }

    public function down(): void
    {
        Schema::table('training_schedules', function (Blueprint $table) {
            foreach (['city','country','is_featured'] as $col) {
                if (Schema::hasColumn('training_schedules', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
