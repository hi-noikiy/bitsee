<?php namespace King\Market\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class AddBlogAuthor extends Migration
{

    public function up()
    {
        if (Schema::hasColumn('rainlab_blog_posts', 'author')) {
            return;
        }

        Schema::table('rainlab_blog_posts', function($table)
        {
            $table->string('author')->nullable();
        });
    }

    public function down()
    {
        
    }

}
