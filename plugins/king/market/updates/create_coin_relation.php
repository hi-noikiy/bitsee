<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCoinRelation extends Migration
{

    public function up()
    {

        Schema::create('coin_market', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('coin_id')->unsigned();
            $table->integer('market_id')->unsigned();
            $table->primary(['coin_id', 'market_id']);
        });
    }

    public function down()
    {
        Schema::drop('coin_market');
    }

}
