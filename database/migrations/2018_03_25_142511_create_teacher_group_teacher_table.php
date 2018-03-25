<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherGroupTeacherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('group_teacher', function (Blueprint $table) {
            $table->integer('group_id');
            $table->integer('teacher_id');
            $table->primary(['group_id','teacher_id']);

            $table->foreign('group_id')->references('id')
            ->on('teacherGroups')->onDelete('cascade');

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
        Schema::dropIfExists('group_teacher');
    }
}
