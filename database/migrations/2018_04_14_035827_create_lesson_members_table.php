<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLessonMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lessonMembers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('lessonId')->unsigned();
            $table->foreign('lessonId')->references('id')->on('lessons')
                                                            ->onDelete('cascade');

            $table->integer('userId')->unsigned();
            $table->foreign('userId')->references('id')->on('users');

            $table->string('role');  
            $table->boolean('absence')->default(false);
            
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
        Schema::dropIfExists('lessonMembers');
    }
}
