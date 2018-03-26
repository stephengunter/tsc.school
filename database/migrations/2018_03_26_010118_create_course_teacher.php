<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseTeacher extends Migration
{
   
    public function up()
    {
        Schema::create('course_teacher', function (Blueprint $table) {
            $table->integer('course_id');
            $table->integer('teacher_id');
            $table->primary(['course_id','teacher_id']);

            $table->foreign('course_id')->references('id')
            ->on('courses')->onDelete('cascade');

            $table->foreign('teacher_id')->references('userId')
            ->on('teachers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_teacher');
    }
}
