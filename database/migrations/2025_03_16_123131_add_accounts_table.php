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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('account_prefix', 6)->nullable();
            $table->string('account_number', 45)->nullable();
            $table->string('account_bank_code', 4)->nullable();
            $table->string('account_iban', 34)->nullable();
            $table->string('account_swift', 11)->nullable();
            $table->string('access_token', 150)->nullable();
            $table->date('access_token_valid_until')->nullable();
            $table->string('control_token', 150)->nullable();
            $table->date('control_token_valid_until')->nullable();
            $table->string('account_abbr', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
