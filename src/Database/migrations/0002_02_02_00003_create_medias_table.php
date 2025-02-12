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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'image' ou 'video'
            $table->string('title')->nullable();
            $table->string('url'); // URL du média
            $table->text('description')->nullable();
            $table->string('category')->nullable();
            $table->string('embed_url')->nullable(); // URL pour les vidéos YouTube/Vimeo
            $table->unsignedInteger('duration')->nullable(); // Durée en secondes (pour les vidéos)
            $table->nullableMorphs('mediable'); // Relation polymorphique
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};