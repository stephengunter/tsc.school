<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsTable extends Migration
{
    
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('courseId')->unsigned(); 
            $table->integer('userId')->unsigned(); 
            $table->integer('status'); 
            $table->decimal('score', 8, 2)->nullable(); 
            
            $table->date('joinDate');
            $table->date('quitDate')->nullable();
            
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
        Schema::dropIfExists('students');
    }
}
