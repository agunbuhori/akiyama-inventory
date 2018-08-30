<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShiftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('code');
            $table->string('jan_code', 50);
            $table->smallInteger('amount')->default(0);
            $table->integer('from_store')->unsigned();
            $table->integer('to_store')->unsigned();
            $table->integer('available_amount')->unsigned();
            $table->enum('status', ['pending', 'acc', 'fail', 'waiting', 'done'])->default('pending');
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
        Schema::dropIfExists('shifts');
    }
}
