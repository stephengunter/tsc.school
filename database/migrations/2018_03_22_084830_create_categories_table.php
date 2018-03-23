<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('parent')->unsigned()->default(0);
            
            $table->string('name');		
            $table->string('code')->nullable(); 	
            $table->integer('importance')->default(0);
            $table->string('icon')->nullable(); 

            $table->boolean('top')->default(false);  
            $table->boolean('active')->default(true);
            $table->boolean('removed')->default(false);		

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
        Schema::dropIfExists('categories');
    }
}
