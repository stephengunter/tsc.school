<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoryCourse extends Migration
{
    public function up()
    {
        Schema::create('category_course', function (Blueprint $table) {
            $table->integer('category_id');
            $table->integer('course_id');
            $table->primary(['category_id','course_id']);

            $table->foreign('category_id')->references('id')
            ->on('categories')->onDelete('cascade');

            $table->foreign('course_id')->references('id')
            ->on('courses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_course');
    }
}
