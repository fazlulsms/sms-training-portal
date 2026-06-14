<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('designation');
            $table->string('linkedin_url')->nullable()->after('department');
            $table->string('preferred_language', 10)->default('en')->after('linkedin_url');
            $table->text('bio')->nullable()->after('preferred_language');
            $table->string('photo_path')->nullable()->after('bio');
            $table->string('emergency_contact_name')->nullable()->after('photo_path');
            $table->string('emergency_contact_phone', 50)->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'department', 'linkedin_url', 'preferred_language',
                'bio', 'photo_path', 'emergency_contact_name', 'emergency_contact_phone',
            ]);
        });
    }
};
