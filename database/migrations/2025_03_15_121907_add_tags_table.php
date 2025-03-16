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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('type', 150);
            $table->string('key', 150)->unique();
            $table->timestamps();
            $table->boolean('active')->default(true);
        });

        Schema::create('tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
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
        Schema::dropIfExists('tags');
        Schema::dropIfExists('tag_translations');
    }
};
