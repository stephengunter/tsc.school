<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quits', function (Blueprint $table) {
            $table->integer('signupId')->unsigned();
			$table->primary('signupId');
			$table->foreign('signupId')->references('id')->on('signups')->onDelete('cascade');

            $table->date('date');
            $table->decimal('tuitions', 8, 2);  
            $table->decimal('fee', 8, 2)->default(0)->nullable();

            $table->integer('paywayId');
            $table->string('account')->nullable();
            $table->integer('status')->default(0); 
           

            $table->boolean('reviewed')->default(false);
            $table->integer('reviewedBy')->unsigned()->nullable();
            
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
        Schema::dropIfExists('quits');
    }
}
