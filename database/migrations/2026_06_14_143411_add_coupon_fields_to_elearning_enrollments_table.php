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
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->string('coupon_code', 50)->nullable()->after('pre_questions');
            $table->decimal('original_amount_before_discount', 10, 2)->nullable()->after('coupon_code');
            $table->decimal('coupon_discount', 10, 2)->default(0)->after('original_amount_before_discount');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->dropColumn(['coupon_code', 'original_amount_before_discount', 'coupon_discount']);
        });
    }
};
