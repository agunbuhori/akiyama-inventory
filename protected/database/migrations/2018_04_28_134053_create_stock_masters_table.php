<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_masters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('season', 10)->nullable();
            $table->string('code', 30)->nullable();
            $table->string('brand')->nullable();
            $table->string('version', 50)->nullable();
            $table->string('size', 30)->nullable();
            $table->string('section', 10)->nullable();
            $table->string('series', 10)->nullable();
            $table->string('rim', 10)->nullable();
            $table->string('jan_code', 50)->nullable();
            $table->string('launch', 20)->nullable();
            $table->integer('price')->default(0);
            $table->string('type', 20);
            $table->integer('user_id')->unsigned();
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
        Schema::dropIfExists('stock_masters');
    }
}
