<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesTable extends Migration
{
   
    public function up()
    {
        Schema::create('processes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('courseId')->unsigned(); 
            $table->foreign('courseId')->references('id')->on('courses')->onDelete('cascade');


            $table->integer('order')->unsigned(); 
            $table->string('title');
            $table->text('content')->nullable(); 
            $table->string('materials')->nullable();
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
        Schema::dropIfExists('processes');
    }
}
