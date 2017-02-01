<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSectionsTable extends Migration
{
    /**
     * Run the migrations.
     * @table sections
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('type');
            $table->string('location', 100);
            $table->integer('gsi');
            $table->integer('second_gsi');
            $table->integer('max_las');
            $table->integer('mon');
            $table->integer('tue');
            $table->integer('wed');
            $table->integer('thu');
            $table->integer('fri');
            $table->integer('sat');
            $table->integer('sun');
            $table->string('start_time', 100);
            $table->string('end_time', 100);
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
       Schema::dropIfExists('sections');
     }
}
