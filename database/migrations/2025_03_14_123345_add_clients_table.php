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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('representative_client_id')->nullable()->constrained('clients');
            $table->foreignId('client_lead_id')->nullable();
            $table->string('name', 500);
            $table->string('company_number', 150)->unique()->nullable();
            $table->string('billing_address_city', 150)->nullable();
            $table->string('billing_address_street', 500)->nullable();
            $table->string('billing_address_postal_code', 45)->nullable();
            $table->string('account_prefix', 6)->nullable();
            $table->string('account_number', 45)->nullable();
            $table->string('account_bank_code', 4)->nullable();
            $table->string('account_iban', 34)->nullable();
            $table->string('account_swift', 11)->nullable();
            $table->string('billing_address_house_number', 150)->nullable();
            $table->string('tax_identification_number', 150)->nullable();
            $table->double('balance')->nullable();
            $table->smallInteger('units_count')->nullable();
            $table->date('date_lead_conversion')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->uuid('crm_client_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
