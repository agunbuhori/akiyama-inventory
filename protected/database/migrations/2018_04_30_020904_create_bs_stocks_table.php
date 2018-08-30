<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBsStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bs_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_code', 20)->nullable();
            $table->string('company_name')->nullable();
            $table->date('receipt_date')->nullable();
            $table->string('receipt_number', 10)->nullable();
            $table->double('article')->nullable();
            $table->string('group')->nullable();
            $table->string('stock_code')->nullable();
            $table->string('jan_code')->nullable();
            $table->string('stock_name')->nullable();
            $table->smallInteger('amount')->default(0);
            $table->integer('sell_price')->default(0);
            $table->integer('basic_price')->default(0);
            $table->integer('barang_titip')->nullable();
            $table->text('memo')->nullable();
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
        Schema::dropIfExists('bs_stocks');
    }
}
