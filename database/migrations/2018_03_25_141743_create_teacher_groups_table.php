<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeacherGroupsTable extends Migration
{
   
    public function up()
    {
        Schema::create('teacherGroups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('centerId')->unsigned();
            
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->boolean('removed')->default(false);
            $table->boolean('active')->default(true);
            
            $table->integer('updatedBy')->unsigned()->nullable();
			$table->string('ps')->nullable();
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
        Schema::dropIfExists('teacherGroups');
    }
}
