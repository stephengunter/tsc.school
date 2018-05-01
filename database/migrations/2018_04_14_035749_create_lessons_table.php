<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('courseId')->unsigned();
            $table->foreign('courseId')->references('id')->on('courses')->onDelete('cascade');
            
            $table->integer('status')->default(0);    
            $table->string('location')->nullable(); 
            $table->integer('on')->nullable();
            $table->integer('off')->nullable();

            $table->date('date');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->text('materials')->nullable();

            $table->boolean('reviewed')->default(false);
            $table->integer('reviewedBy')->unsigned()->nullable();
            
            $table->string('ps')->nullable();
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
        Schema::dropIfExists('lessons');
    }
}
