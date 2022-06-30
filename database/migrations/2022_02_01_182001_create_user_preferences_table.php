<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserPreferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('min_price')->nullable();
            $table->decimal('max_price')->nullable();
            $table->integer('min_calories')->nullable();
            $table->integer('max_calories')->nullable();
            $table->integer('min_difficulty_of_execution')->nullable();
            $table->integer('max_difficulty_of_execution')->nullable();
            $table->integer('min_execution_time')->nullable();
            $table->integer('max_execution_time')->nullable();
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
        Schema::dropIfExists('user_preferences');
    }
}
