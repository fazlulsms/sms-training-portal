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
            $table->string('gender', 30)->nullable()->after('phone');
            $table->string('industry', 150)->nullable()->after('designation');
            $table->string('experience_years', 30)->nullable()->after('industry');
            $table->string('city', 100)->nullable()->after('country');
            $table->string('full_address', 400)->nullable()->after('city');
            $table->string('emergency_contact_name', 150)->nullable()->after('full_address');
            $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');
            $table->text('special_requirements')->nullable()->after('emergency_contact_phone');
            $table->string('referral_source', 100)->nullable()->after('special_requirements');
            $table->text('pre_questions')->nullable()->after('referral_source');
        });
    }

    public function down(): void
    {
        Schema::table('elearning_enrollments', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'industry', 'experience_years', 'city', 'full_address',
                'emergency_contact_name', 'emergency_contact_phone',
                'special_requirements', 'referral_source', 'pre_questions',
            ]);
        });
    }
};
