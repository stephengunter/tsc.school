<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteersTable extends Migration
{
    
    public function up()
    {
        Schema::create('volunteers', function (Blueprint $table) {
            $table->integer('userId')->unsigned();
			$table->primary('userId');
			$table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
            
            $table->boolean('active')->default(false);
            $table->boolean('removed')->default(false);
            $table->date('joinDate')->nullable();
            $table->integer('updatedBy')->unsigned()->nullable();
            $table->string('time')->nullable(); 
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
        Schema::dropIfExists('volunteers');
    }
}
