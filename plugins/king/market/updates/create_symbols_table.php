<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateSymbolsTable extends Migration
{

    public function up()
    {
        Schema::create('symbols', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('parent_id')->unsigned()->index()->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->timestamps();
        });

        Schema::create('markets_symbols', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('market_id')->unsigned();
            $table->integer('symbol_id')->unsigned();
            $table->primary(['market_id', 'symbol_id']);
        });
    }

    public function down()
    {
        Schema::drop('symbols');
        Schema::drop('markets_symbols');
    }

}
