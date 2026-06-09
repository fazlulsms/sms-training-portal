<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('enrollment_id')->nullable()->after('id');
            $table->unsignedBigInteger('elearning_enrollment_id')->nullable()->after('enrollment_id');
            $table->boolean('payment_confirmed_email_sent')->default(false)->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['enrollment_id', 'elearning_enrollment_id', 'payment_confirmed_email_sent']);
        });
    }
};
