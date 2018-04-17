<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaywaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('need_account')->default(false);
            $table->boolean('pay')->default(true);   
            $table->boolean('back')->default(true);  
            $table->boolean('auto')->default(false); 

            $table->decimal('fee', 8, 2)->nullable(); 
            $table->boolean('fee_percents')->default(false);   
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
        Schema::dropIfExists('payways');
    }
}
