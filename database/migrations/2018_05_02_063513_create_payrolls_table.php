<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('centerId')->unsigned();
            $table->integer('userId')->unsigned();
            $table->string('wageName');
            $table->integer('year')->unsigned();
            $table->integer('month')->unsigned();

            $table->boolean('reviewed')->default(false);
            $table->integer('reviewedBy')->unsigned()->nullable();

            $table->integer('status')->default(0); 

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
        Schema::dropIfExists('payrolls');
    }
}
