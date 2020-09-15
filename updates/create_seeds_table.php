<?php
namespace OFFLINE\Igloo\Updates;

use October\Rain\Database\Updates\Migration;
use Schema;

class CreateSeedsTable extends Migration
{
    public function up()
    {
        Schema::create(
            'offline_seeder_seeds',
            function ($table) {
                $table->engine = 'InnoDB';
                $table->increments('id')->unsigned();
                $table->string('seeder');
                $table->timestamp('seeded_at')->nullable();
            }
        );
    }

    public function down()
    {
        Schema::dropIfExists('offline_seeder_seeds');
    }
}
