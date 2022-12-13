<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMultipleStopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('multiple_stops', function (Blueprint $table) {
            $table->id();
            $table->string('latitude');
            $table->string('longitude');
            $table->unsignedBigInteger('routes_id');
            $table->foreign('routes_id')->references('id')->on('routes')->onDelete('cascade');
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
        Schema::dropIfExists('multiple_stops');
    }
}
