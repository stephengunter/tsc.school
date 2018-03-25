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
            $table->integer('userId')->unsigned();

            $table->string('bank')->nullable();
            $table->string('branch')->nullable();
            $table->string('owner')->nullable();
            $table->string('account');
            $table->decimal('money', 8, 2); 
            
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
