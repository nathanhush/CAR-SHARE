<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->integer("time_pickup");
            $table->integer("date");
            $table->integer("longitude");
            $table->integer("latitude");
            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references('id')->on('users')->onDelete('cascade');
            $table->integer("ride_status");
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
        Schema::dropIfExists('requests');
    }
}
