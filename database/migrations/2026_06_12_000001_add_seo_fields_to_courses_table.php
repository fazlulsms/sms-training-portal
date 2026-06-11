<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (!Schema::hasColumn('courses', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
            if (!Schema::hasColumn('courses', 'display_order')) {
                $table->unsignedInteger('display_order')->default(0)->after('is_featured');
            }
            if (!Schema::hasColumn('courses', 'featured_order')) {
                $table->unsignedInteger('featured_order')->default(0)->after('display_order');
            }
            if (!Schema::hasColumn('courses', 'course_video_url')) {
                $table->string('course_video_url')->nullable()->after('featured_order');
            }
            if (!Schema::hasColumn('courses', 'faq')) {
                $table->longText('faq')->nullable()->after('course_video_url');
            }
            if (!Schema::hasColumn('courses', 'certification_info')) {
                $table->text('certification_info')->nullable()->after('faq');
            }
            if (!Schema::hasColumn('courses', 'seo_title')) {
                $table->string('seo_title')->nullable()->after('certification_info');
            }
            if (!Schema::hasColumn('courses', 'seo_description')) {
                $table->text('seo_description')->nullable()->after('seo_title');
            }
            if (!Schema::hasColumn('courses', 'seo_keywords')) {
                $table->string('seo_keywords')->nullable()->after('seo_description');
            }
            if (!Schema::hasColumn('courses', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable()->after('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $cols = ['slug','display_order','featured_order','course_video_url',
                     'faq','certification_info','seo_title','seo_description','seo_keywords','category_id'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('courses', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
