<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('grand_total');
            }
        });

        // Back-fill from grand_total for any existing rows
        DB::statement('UPDATE invoices SET total_amount = grand_total WHERE total_amount = 0');
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'total_amount')) {
                $table->dropColumn('total_amount');
            }
        });
    }
};
