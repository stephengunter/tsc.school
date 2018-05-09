<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->integer('userId')->unsigned();
			$table->primary('userId');
			$table->foreign('userId')->references('id')->on('users')->onDelete('cascade');

			$table->string('fullname')->nullable();
			$table->string('sid');
			$table->boolean('gender')->default(true);
			$table->date('dob')->nullable();
			

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
        Schema::dropIfExists('profiles');
    }
}
