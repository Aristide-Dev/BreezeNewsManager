<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable()->unique();
            $table->text('excerpt')->nullable();
            $table->text('content')->nullable();
            $table->string('image')->nullable(); // URL de l'image principale
            $table->string('category')->nullable();
            $table->json('tags')->nullable(); // Tags sous forme de chaîne JSON
            $table->boolean('featured')->default(false); // à la une
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('read_time')->default(0); // Temps de lecture en minutes
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('news');
    }
} 