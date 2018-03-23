<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centers', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('head')->default(false);
            $table->boolean('oversea')->default(false);
            $table->integer('areaId')->unsigned()->nullable();

            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->string('courseTel')->nullable();

            $table->text('rule')->nullable();
            
            $table->boolean('active')->default(true);
            $table->boolean('removed')->default(false);
            
            $table->integer('importance')->default(0);
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
        Schema::dropIfExists('centers');
    }
}
