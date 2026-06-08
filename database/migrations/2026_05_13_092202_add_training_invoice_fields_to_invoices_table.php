<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'client_country')) {
                $table->string('client_country')->nullable()->after('client_address');
            }

            if (!Schema::hasColumn('invoices', 'contact_person')) {
                $table->string('contact_person')->nullable()->after('client_company');
            }

            if (!Schema::hasColumn('invoices', 'client_reference_number')) {
                $table->string('client_reference_number')->nullable()->after('contact_person');
            }

            if (!Schema::hasColumn('invoices', 'service_type')) {
                $table->string('service_type')->nullable()->after('client_reference_number');
            }

            if (!Schema::hasColumn('invoices', 'training_name')) {
                $table->string('training_name')->nullable()->after('service_type');
            }

            if (!Schema::hasColumn('invoices', 'training_date')) {
                $table->string('training_date')->nullable()->after('training_name');
            }

            if (!Schema::hasColumn('invoices', 'training_duration')) {
                $table->string('training_duration')->nullable()->after('training_date');
            }

            if (!Schema::hasColumn('invoices', 'training_method_venue')) {
                $table->string('training_method_venue')->nullable()->after('training_duration');
            }

            if (!Schema::hasColumn('invoices', 'number_of_participants')) {
                $table->integer('number_of_participants')->default(1)->after('training_method_venue');
            }

            if (!Schema::hasColumn('invoices', 'fee_per_person')) {
                $table->decimal('fee_per_person', 12, 2)->default(0)->after('number_of_participants');
            }

            if (!Schema::hasColumn('invoices', 'charge_for')) {
                $table->decimal('charge_for', 12, 2)->default(0)->after('fee_per_person');
            }

            if (!Schema::hasColumn('invoices', 'discount_percent')) {
                $table->decimal('discount_percent', 8, 2)->default(0)->after('charge_for');
            }

            if (!Schema::hasColumn('invoices', 'amount_in_words')) {
                $table->string('amount_in_words')->nullable()->after('grand_total');
            }

            if (!Schema::hasColumn('invoices', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('amount_paid');
            }

            if (!Schema::hasColumn('invoices', 'prepared_by')) {
                $table->string('prepared_by')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $columns = [
                'client_country',
                'contact_person',
                'client_reference_number',
                'service_type',
                'training_name',
                'training_date',
                'training_duration',
                'training_method_venue',
                'number_of_participants',
                'fee_per_person',
                'charge_for',
                'discount_percent',
                'amount_in_words',
                'payment_method',
                'prepared_by',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};