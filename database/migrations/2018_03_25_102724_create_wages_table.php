<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWagesTable extends Migration
{
    
    public function up()
    {
        Schema::create('wages', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name');
            $table->string('code')->nullable();
            
            $table->decimal('small_day', 8, 2)->nullable(); 
            $table->decimal('small_night', 8, 2)->nullable();
            $table->decimal('small_holiday', 8, 2)->nullable();

            $table->decimal('big_day', 8, 2)->nullable();
            $table->decimal('big_night', 8, 2)->nullable(); 
            $table->decimal('big_holiday', 8, 2)->nullable(); 

            $table->decimal('lecture', 8, 2)->nullable(); 

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
        Schema::dropIfExists('wages');
    }
}
