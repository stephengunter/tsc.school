<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCenterVolunteerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('center_volunteer', function (Blueprint $table) {
            $table->integer('center_id');
            $table->integer('volunteer_id');
            $table->primary(['center_id','volunteer_id']);

            $table->foreign('center_id')->references('id')
            ->on('centers')->onDelete('cascade');

            $table->foreign('volunteer_id')->references('userId')
            ->on('volunteers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('center_volunteer');
    }
}
