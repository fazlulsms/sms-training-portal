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
            $table->string('cover_image')->nullable()->after('banner_image');
            $table->string('cover_thumbnail')->nullable()->after('cover_image');
            $table->boolean('cover_generated_by_ai')->default(false)->after('cover_thumbnail');
            $table->text('cover_prompt')->nullable()->after('cover_generated_by_ai');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'cover_thumbnail', 'cover_generated_by_ai', 'cover_prompt']);
        });
    }
};
