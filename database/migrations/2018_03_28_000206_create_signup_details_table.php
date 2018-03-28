<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignupDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signupDetails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('signupId')->unsigned();
            $table->integer('courseId')->unsigned();
			
            $table->decimal('tuition', 8, 2);  
            $table->decimal('cost', 8, 2);  

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
        Schema::dropIfExists('signupDetails');
    }
}
