<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateTickerTable extends Migration
{

    public function up()
    {
        Schema::create('tickers', function($table)
        {
            $table->engine = 'InnoDB';
            $table->unsignedBigInteger('id')->primary();
            $table->double('high')->nullable();
            $table->double('low')->nullable();
            $table->double('vol')->nullable();
            $table->double('open')->nullable();
            $table->double('close')->nullable();
            $table->enum('symbol', ['BTC/USDT', 'LTC/BTC']);
            $table->enum('market', ['huobipro', 'cokex']);
            $table->dateTime('date_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('tickers');
    }

}