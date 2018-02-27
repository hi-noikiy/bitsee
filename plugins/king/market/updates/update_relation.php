<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class UpdateRelation extends Migration
{

    public function up()
    {
        Schema::table('symbols', function($table)
        {
            $table->integer('market_id');
            $table->renameColumn('name', 'symbol');
            $table->float('maker')->nullable();
            $table->float('taker')->nullable();
            $table->float('lot')->nullable();
            $table->char('base', 50);
            $table->char('quote', 50);
            $table->float('limits_amount_max')->nullable();
            $table->float('limits_amount_min')->nullable();
            $table->float('limits_cost_max')->nullable();
            $table->float('limits_cost_min')->nullable();
            $table->float('limits_price_max')->nullable();
            $table->float('limits_price_min')->nullable();
            $table->float('precision_amount')->nullable();
            $table->float('precision_price')->nullable();
        });
    }

    public function down()
    {
    }

}