<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove old records (narration/ai_explanation types being replaced)
        DB::table('lesson_audio')->delete();

        // Add a standalone index on lesson_id so MySQL keeps FK coverage after unique drop
        Schema::table('lesson_audio', function (Blueprint $table) {
            $table->index('lesson_id', 'lesson_audio_lesson_id_idx');
        });

        Schema::table('lesson_audio', function (Blueprint $table) {
            // Now the unique constraint can be dropped (FK is covered by the new index)
            $table->dropUnique('lesson_audio_lesson_id_audio_type_language_unique');

            // Add block_id nullable FK — lesson_recap has no block, ai_coach has one
            $table->unsignedBigInteger('block_id')->nullable()->after('lesson_id');
            $table->foreign('block_id')->references('id')->on('lesson_blocks')->onDelete('cascade');
        });

        // Modify enum — must use raw SQL (Blueprint::change() on ENUMs requires doctrine/dbal)
        DB::statement("ALTER TABLE lesson_audio MODIFY audio_type ENUM('ai_coach','lesson_recap') NOT NULL");
    }

    public function down(): void
    {
        DB::table('lesson_audio')->delete();

        Schema::table('lesson_audio', function (Blueprint $table) {
            $table->dropForeign(['block_id']);
            $table->dropColumn('block_id');
        });

        DB::statement("ALTER TABLE lesson_audio MODIFY audio_type ENUM('narration','ai_explanation') NOT NULL");

        Schema::table('lesson_audio', function (Blueprint $table) {
            $table->unique(['lesson_id', 'audio_type', 'language']);
        });
    }
};
