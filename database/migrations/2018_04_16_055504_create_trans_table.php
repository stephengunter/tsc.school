<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trans', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date');
            $table->integer('signupDetailId')->unsigned();
            $table->integer('courseId')->unsigned();

           
            $table->decimal('tuition', 8, 2)->default(0); 
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
        Schema::dropIfExists('trans');
    }
}
