<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuitDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quitDetails', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('signupId')->unsigned();
            $table->foreign('signupId')->references('signupid')->on('quits')->onDelete('cascade');

            $table->integer('signupDetailId')->unsigned();
            $table->integer('percents')->unsigned();
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
        Schema::dropIfExists('quitDetails');
    }
}
