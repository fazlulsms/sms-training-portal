<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            if (!Schema::hasColumn('trainers', 'photo')) {
                $table->string('photo')->nullable()->after('qualification');
            }
            if (!Schema::hasColumn('trainers', 'short_bio')) {
                $table->text('short_bio')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('trainers', 'expertise_areas')) {
                $table->text('expertise_areas')->nullable()->after('short_bio');
            }
            if (!Schema::hasColumn('trainers', 'certifications')) {
                $table->text('certifications')->nullable()->after('expertise_areas');
            }
            if (!Schema::hasColumn('trainers', 'experience')) {
                $table->string('experience')->nullable()->after('certifications');
            }
            if (!Schema::hasColumn('trainers', 'is_public')) {
                $table->boolean('is_public')->default(false)->after('experience');
            }
            if (!Schema::hasColumn('trainers', 'display_order')) {
                $table->unsignedInteger('display_order')->default(0)->after('is_public');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $cols = ['photo','short_bio','expertise_areas','certifications','experience','is_public','display_order'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('trainers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
