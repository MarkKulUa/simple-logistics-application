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
        Schema::create('offer_states', function (Blueprint $table) {
            $table->id();
            $table->string('translation_key', 150);
            $table->string('description', 150);
            $table->timestamps();
        });

        Schema::create('offer_reject_states', function (Blueprint $table) {
            $table->id();
            $table->string('translation_key', 150);
            $table->string('description', 150);
            $table->timestamps();
        });

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('client_id')->nullable()->constrained('clients');
//            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->foreignId('offer_state_id')->constrained('offer_states');
            $table->foreignId('rejected_state_id')->nullable()->constrained('offer_reject_states');
            $table->foreignId('end_of_cooperation_tag_id')->nullable()->constrained('end_of_cooperation_tags');
            $table->decimal('price', 19, 4);
            $table->char('currency_iso_4217', 3);
            $table->date('date_issued');
            $table->date('date_signed')->nullable();
            $table->date('date_confirmed')->nullable();
            $table->date('date_active_from');
            $table->date('date_active_to')->nullable();
            $table->text('notes')->nullable();
            $table->string('header_client_name', 150);
            $table->string('header_client_street', 150);
            $table->string('header_client_postal_code', 150);
            $table->string('header_client_city', 150);
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_reject_states');
        Schema::dropIfExists('offer_states');
        Schema::dropIfExists('offers');
    }
};
