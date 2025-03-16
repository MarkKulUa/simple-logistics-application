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
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('trainer_id')->nullable()->constrained('partners');
            $table->foreignId('ending_reason_tag_id')->nullable()->constrained('tags')->onDelete('cascade');
            $table->string('first_name', 150)->nullable();
            $table->string('last_name', 150)->nullable();
            $table->string('company_name', 500)->nullable();
            $table->string('company_number', 100)->nullable();
            $table->string('email', 320)->nullable();
            $table->string('phone', 45)->nullable();
            $table->string('photo_location', 500)->nullable();
            $table->string('billing_address_city', 150)->nullable();
            $table->string('billing_address_street', 500)->nullable();
            $table->string('billing_address_house_number', 150)->nullable();
            $table->string('billing_address_postal_code', 45)->nullable();
            $table->date('contract_signed')->nullable();
            $table->date('contract_ended')->nullable();
            $table->boolean('is_insured')->nullable();
            $table->date('insurance_expiration_date')->nullable();
            $table->string('account_prefix', 6)->nullable();
            $table->string('account_number', 45)->nullable();
            $table->string('account_bank_code', 4)->nullable();
            $table->string('suite_guid', 150)->nullable();
            $table->string('account_iban', 34)->nullable();
            $table->string('account_swift', 11)->nullable();
            $table->string('account_bank_owner', 150)->nullable();
            $table->string('vat_number', 150)->nullable();
            $table->decimal('guaranteed_earnings', 19, 4)->nullable();
            $table->boolean('is_trainer')->nullable();
            $table->date('training_day_date')->nullable();
            $table->boolean('do_not_contact')->nullable();
            $table->text('notes')->nullable();
            $table->text('ending_reason')->nullable();
            $table->text('ending_note')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->uuid('crm_partner_id')->nullable();
            $table->enum('partner_type', ['individual', 'company'])->nullable();
            $table->string('country_of_birth', 150)->nullable();
            $table->string('birth_city', 150)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('country_for_id', 150)->nullable();
            $table->string('residency', 150)->nullable();
            $table->integer('vat_payers_number')->nullable();
            $table->string('street', 150)->nullable();
            $table->string('descriptive_number', 150)->nullable();
            $table->string('apartment', 150)->nullable();
            $table->string('postcode', 150)->nullable();
            $table->string('city', 150)->nullable();
            $table->string('area', 150)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('preferred_location', 600)->nullable();
            $table->string('product_type', 50)->nullable();
            $table->text('requested_days')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
