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
        Schema::table('training_schedules', function (Blueprint $table) {
            $table->boolean('is_public')->default(false)->after('status');
            $table->string('schedule_status')->default('Upcoming')->after('is_public'); // Upcoming, Running, Completed, Cancelled
            $table->date('registration_deadline')->nullable()->after('schedule_status');
            $table->unsignedInteger('available_seats')->nullable()->after('registration_deadline');
            $table->decimal('discount_fee', 10, 2)->nullable()->after('available_seats');
            $table->time('time_start')->nullable()->after('discount_fee');
            $table->time('time_end')->nullable()->after('time_start');
        });
    }

    public function down(): void
    {
        Schema::table('training_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'is_public','schedule_status','registration_deadline',
                'available_seats','discount_fee','time_start','time_end',
            ]);
        });
    }
};
