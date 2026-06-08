<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            // slug & category already exist — only add truly new columns
            $table->string('delivery_type')->default('Instructor-Led')->after('category');
            $table->string('language')->default('English')->after('delivery_type');
            $table->string('banner_image')->nullable()->after('language');
            $table->string('short_description', 500)->nullable()->after('banner_image');
            $table->text('full_description')->nullable()->after('short_description');
            $table->text('learning_objectives')->nullable()->after('full_description');
            $table->text('course_outline')->nullable()->after('learning_objectives');
            $table->text('who_should_attend')->nullable()->after('course_outline');
            $table->text('prerequisites')->nullable()->after('who_should_attend');
            $table->string('certificate_type')->nullable()->after('prerequisites');
            $table->boolean('is_public')->default(false)->after('certificate_type');
            $table->boolean('is_featured')->default(false)->after('is_public');
            $table->decimal('public_price', 10, 2)->nullable()->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_type','language','banner_image',
                'short_description','full_description','learning_objectives',
                'course_outline','who_should_attend','prerequisites',
                'certificate_type','is_public','is_featured','public_price',
            ]);
        });
    }
};
