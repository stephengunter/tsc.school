<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classTimes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('courseId')->unsigned(); 
            $table->foreign('courseId')->references('id')->on('courses')->onDelete('cascade');

            $table->integer('weekdayId')->unsigned(); 
            $table->integer('on')->unsigned(); 
            $table->integer('off')->unsigned(); 
            $table->string('location')->nullable();

            $table->integer('updatedBy')->unsigned()->nullable();
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
        Schema::dropIfExists('classTimes');
    }
}
