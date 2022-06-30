<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldFunctionForPostgressql extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(env('DB_CONNECTION') === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement(
                "CREATE OR REPLACE FUNCTION field(anyelement, VARIADIC anyarray) RETURNS bigint AS $$
                          SELECT n FROM (
                            SELECT row_number() OVER () AS n, x FROM unnest($2) x)
                              numbered WHERE numbered.x = $1;
                        $$ LANGUAGE SQL IMMUTABLE STRICT;"
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
