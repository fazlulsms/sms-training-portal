<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->string('invoice_number')->unique();

            $table->enum('invoice_type', ['Individual', 'Corporate'])->default('Individual');

            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone')->nullable();
            $table->text('client_address')->nullable();
            $table->string('client_company')->nullable();

            $table->date('invoice_date');
            $table->date('due_date')->nullable();

            $table->string('currency')->default('BDT');

            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('vat_percent', 8, 2)->default(0);
            $table->decimal('vat_amount', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);

            $table->enum('payment_status', ['Unpaid', 'Partial', 'Paid', 'Cancelled'])->default('Unpaid');
            $table->decimal('amount_paid', 12, 2)->default(0);

            $table->text('notes')->nullable();
            $table->text('terms')->nullable();

            $table->enum('status', ['Draft', 'Issued', 'Cancelled'])->default('Draft');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};