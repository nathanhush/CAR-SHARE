<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->integer("status");
            $table->integer("rating");
            $table->string("reviews");
            // $table->unsignedBigInteger("request_id");
            $table->unsignedBigInteger("route_id");
            // $table->foreign("request_id")->references('id')->on('requests')->onDelete('cascade');
            $table->foreign("route_id")->references('id')->on('routes')->onDelete('cascade');
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
        Schema::dropIfExists('rides');
    }
}
