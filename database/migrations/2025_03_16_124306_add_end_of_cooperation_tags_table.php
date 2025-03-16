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
        Schema::create('end_of_cooperation_tags', function (Blueprint $table) {
            $table->id();
            $table->string('key', 150);
            $table->timestamps();
        });

        Schema::create('end_of_cooperation_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('end_of_cooperation_tag_id')->constrained('end_of_cooperation_tags')->onDelete('cascade');
            $table->string('language_ietf', 5);
            $table->boolean('is_default')->default(false);
            $table->string('tag_name', 100);
            $table->string('tag_description', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('end_of_cooperation_tags');
        Schema::dropIfExists('end_of_cooperation_tag_translations');
    }
};
