<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseVolunteer extends Migration
{
    public function up()
    {
        Schema::create('course_volunteer', function (Blueprint $table) {
            $table->integer('course_id');
            $table->integer('volunteer_id');
            $table->primary(['course_id','volunteer_id']);

            $table->foreign('course_id')->references('id')
            ->on('courses')->onDelete('cascade');

            $table->foreign('volunteer_id')->references('userId')
            ->on('volunteers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_volunteer');
    }
}
