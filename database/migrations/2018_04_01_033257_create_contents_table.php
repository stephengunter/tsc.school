<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->increments('id');

            $table->string('key');
            $table->string('title'); 
            $table->text('content'); 

            $table->integer('importance')->default(0);

            $table->boolean('reviewed')->default(false);
            $table->integer('reviewedBy')->unsigned()->nullable();
            
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
        Schema::dropIfExists('contents');
    }
}
