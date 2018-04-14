<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocsTable extends Migration
{
    
    public function up()
    {
        Schema::create('docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('path');
            $table->string('type');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('ps')->nullable();

            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('peviewPath')->nullable();

            $table->integer('importance')->default(0);
            $table->integer('updatedBy')->unsigned()->nullable();

            $table->string('roles')->nullable();

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
        Schema::dropIfExists('docs');
    }
}
