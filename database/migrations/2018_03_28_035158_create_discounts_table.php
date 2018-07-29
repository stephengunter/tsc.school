<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{
   

    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');  //east , west ,oversea
            $table->string('name');
            $table->string('code');
            $table->integer('min')->default(1);
            $table->integer('age')->default(0);
            $table->integer('pointOne');
            $table->integer('pointTwo');
            $table->boolean('prove');
            $table->boolean('top')->default(false);
            $table->string('ps')->nullable();
            $table->boolean('active');
            $table->boolean('removed')->default(false);
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
        Schema::dropIfExists('discounts');
    }
}
