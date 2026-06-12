<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            if (! Schema::hasColumn('trainers', 'professional_highlights')) {
                $table->text('professional_highlights')->nullable()->after('experience');
            }
            if (! Schema::hasColumn('trainers', 'industries_served')) {
                $table->text('industries_served')->nullable()->after('professional_highlights');
            }
            if (! Schema::hasColumn('trainers', 'countries_covered')) {
                $table->string('countries_covered')->nullable()->after('industries_served');
            }
            if (! Schema::hasColumn('trainers', 'languages_spoken')) {
                $table->string('languages_spoken')->nullable()->after('countries_covered');
            }
            if (! Schema::hasColumn('trainers', 'training_specializations')) {
                $table->text('training_specializations')->nullable()->after('languages_spoken');
            }
            if (! Schema::hasColumn('trainers', 'audit_specializations')) {
                $table->text('audit_specializations')->nullable()->after('training_specializations');
            }
            if (! Schema::hasColumn('trainers', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('audit_specializations');
            }
            if (! Schema::hasColumn('trainers', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (! Schema::hasColumn('trainers', 'seo_keywords')) {
                $table->string('seo_keywords')->nullable()->after('seo_description');
            }
            if (! Schema::hasColumn('trainers', 'ai_generated')) {
                $table->boolean('ai_generated')->default(false)->after('seo_keywords');
            }
            if (! Schema::hasColumn('trainers', 'ai_profile_data')) {
                $table->json('ai_profile_data')->nullable()->after('ai_generated');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trainers', function (Blueprint $table) {
            $cols = [
                'professional_highlights', 'industries_served', 'countries_covered',
                'languages_spoken', 'training_specializations', 'audit_specializations',
                'seo_title', 'seo_description', 'seo_keywords', 'ai_generated', 'ai_profile_data',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('trainers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
