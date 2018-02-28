<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateCoinTable extends Migration
{

    public function up()
    {
        Schema::create('coins', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->char('name',20);
            $table->longText('content')->nullable();
            $table->longText('content_html')->nullable();

            $table->char('platform',200);

            $table->char('type',200);

            $table->char('auth',200);

            $table->char('supply',200);

            $table->char('url',200);

            $table->timestamp('published_at')->nullable();
            $table->boolean('published')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop('coins');
    }

}