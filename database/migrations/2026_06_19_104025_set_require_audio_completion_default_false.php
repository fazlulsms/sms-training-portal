<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change column default to false — audio is ambient/optional, not a completion gate
        Schema::table('elearning_lessons', function (Blueprint $table) {
            $table->boolean('require_audio_completion')->default(false)->change();
        });

        // Reset all existing lessons so nothing is blocked by the old default
        DB::table('elearning_lessons')->update(['require_audio_completion' => false]);
    }

    public function down(): void
    {
        Schema::table('elearning_lessons', function (Blueprint $table) {
            $table->boolean('require_audio_completion')->default(true)->change();
        });
    }
};
