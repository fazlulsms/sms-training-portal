<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // This table was created in an early migration (2026_05_15_095001) but is never
    // written to or read by any application code. All quiz attempt logic uses
    // the `quiz_attempts` table via the QuizAttempt model.
    //
    // IMPORTANT: If the table contains rows when this migration runs, it will
    // log a warning and skip the drop to prevent accidental data loss.
    // Manually truncate the table first if you are sure the rows are test data.

    public function up(): void
    {
        if (!Schema::hasTable('elearning_quiz_attempts')) {
            return; // Already gone — nothing to do
        }

        $rowCount = DB::table('elearning_quiz_attempts')->count();

        if ($rowCount > 0) {
            // Log a warning but do not fail — data exists; human review required
            \Illuminate\Support\Facades\Log::warning(
                "Migration skipped drop of `elearning_quiz_attempts`: table has {$rowCount} row(s). " .
                "Verify these rows are test data, truncate manually, then re-run this migration."
            );
            return;
        }

        Schema::dropIfExists('elearning_quiz_attempts');
    }

    public function down(): void
    {
        // Intentionally empty — we do not recreate the old unused table on rollback
    }
};
