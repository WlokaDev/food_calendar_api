<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->json('title')->fulltext();
            $table->json('description')->fulltext();
            $table->decimal('price')->nullable();
            $table->string('currency')->nullable();
            $table->integer('calories')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('difficulty_of_execution');
            $table->integer('execution_time');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recipes');
    }
}
