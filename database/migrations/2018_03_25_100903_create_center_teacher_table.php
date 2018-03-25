<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCenterTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('center_teacher', function (Blueprint $table) {
            $table->integer('center_id');
            $table->integer('teacher_id');
            $table->primary(['center_id','teacher_id']);

            $table->foreign('center_id')->references('id')
            ->on('centers')->onDelete('cascade');

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
        Schema::dropIfExists('center_teacher');
    }
}
