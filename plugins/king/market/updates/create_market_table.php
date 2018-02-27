<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateMarketTable extends Migration
{

    public function up()
    {
        Schema::create('markets', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->char('name',20);
            $table->longText('content')->nullable();
            $table->longText('content_html')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('markets');
    }

}