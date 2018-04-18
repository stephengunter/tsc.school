<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
           
            $table->integer('userId')->unsigned();
			$table->primary('userId');
			$table->foreign('userId')->references('id')->on('users')->onDelete('cascade');


            $table->boolean('active')->default(true);
            $table->boolean('removed')->default(false);
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
        Schema::dropIfExists('admins');
    }
}
