<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('training_schedule_id')->nullable()->after('course_id');
            $table->string('article_type')->default('blog_post')->after('training_schedule_id');
            $table->boolean('ai_generated')->default(false)->after('article_type');
            $table->timestamp('ai_generated_at')->nullable()->after('ai_generated');
            $table->string('og_title')->nullable()->after('seo_description');
            $table->string('og_description')->nullable()->after('og_title');
            $table->text('focus_keywords')->nullable()->after('og_description');
            $table->json('tags')->nullable()->after('focus_keywords');
            $table->text('social_linkedin')->nullable()->after('tags');
            $table->text('social_facebook')->nullable()->after('social_linkedin');
            $table->text('social_twitter')->nullable()->after('social_facebook');
            $table->text('social_instagram')->nullable()->after('social_twitter');
            $table->text('hashtags')->nullable()->after('social_instagram');
            $table->unsignedBigInteger('approved_by')->nullable()->after('hashtags');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->json('change_log')->nullable()->after('approved_at');

            $table->foreign('training_schedule_id')->references('id')->on('training_schedules')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign(['training_schedule_id']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'training_schedule_id', 'article_type', 'ai_generated', 'ai_generated_at',
                'og_title', 'og_description', 'focus_keywords', 'tags',
                'social_linkedin', 'social_facebook', 'social_twitter', 'social_instagram', 'hashtags',
                'approved_by', 'approved_at', 'change_log',
            ]);
        });
    }
};
