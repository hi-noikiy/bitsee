<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class MarketsAddBackendField extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('markets', 'backend')) {
            return;
        }

        Schema::table('markets', function($table)
        {
            $table->char('backend',50)->nullable();
        });
    }

    public function down()
    {
    }

}