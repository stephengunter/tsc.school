<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->integer('userId')->unsigned();
			$table->primary('userId');
			$table->foreign('userId')->references('id')->on('users')->onDelete('cascade');
			
			
			$table->text('experiences')->nullable();
			$table->string('education')->nullable();
			$table->string('certificate')->nullable();
			$table->string('specialty')->nullable();
			$table->string('job')->nullable();
            $table->string('jobtitle')->nullable();
            $table->text('description')->nullable();
            
            $table->boolean('active')->default(false);
            $table->boolean('removed')->default(false);
           
            $table->boolean('reviewed')->default(false);
            $table->integer('reviewedBy')->unsigned()->nullable();
			
			$table->date('joinDate')->nullable();

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
        Schema::dropIfExists('teachers');
    }
}
