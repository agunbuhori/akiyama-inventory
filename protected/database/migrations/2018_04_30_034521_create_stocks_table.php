<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('stock_datetime')->nullable();
            $table->string('jan_code', 50)->nullable();
            $table->string('type', 20)->nullable();
            $table->smallInteger('amount')->nullable();
            $table->integer('price')->nullable();
            $table->integer('receipts')->nullable();
            $table->string('store_code', 10)->nullable();
            $table->enum('arrow', ['in', 'out', 'titip'])->default('in');
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
        Schema::dropIfExists('stocks');
    }
}
