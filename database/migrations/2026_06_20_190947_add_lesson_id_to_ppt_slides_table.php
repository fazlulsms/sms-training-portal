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
        Schema::table('ppt_slides', function (Blueprint $table) {
            $table->unsignedBigInteger('lesson_id')->nullable()->after('ppt_module_id');
        });
    }

    public function down(): void
    {
        Schema::table('ppt_slides', function (Blueprint $table) {
            $table->dropColumn('lesson_id');
        });
    }
};
