<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParentAcceptableChangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parent_acceptable_change', function (Blueprint $table) {
            $table->foreignId('parent_acceptable_change_id')->constrained('acceptable_changes')->cascadeOnDelete();
            $table->foreignId('acceptable_change_id')->constrained('acceptable_changes')->cascadeOnDelete();
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
        Schema::dropIfExists('parent_acceptable_change');
    }
}
