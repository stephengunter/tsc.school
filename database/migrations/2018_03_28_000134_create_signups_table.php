<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signups', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userId')->unsigned();
            $table->date('date')->nullable();
            $table->boolean('net')->default(true);
            $table->decimal('tuitions', 8, 2);  
            $table->decimal('costs', 8, 2);  
            $table->integer('points')->unsigned();
            $table->string('discount')->nullable();
            $table->integer('discountId')->nullable();
            $table->integer('status');

            $table->boolean('payed')->default(false); 

            $table->string('identity_ids')->nullable(); 

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
        Schema::dropIfExists('signups');
    }
}
