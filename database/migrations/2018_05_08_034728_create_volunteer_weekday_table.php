<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVolunteerWeekdayTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('volunteer_weekday', function (Blueprint $table) {
            $table->integer('volunteer_id');
            $table->integer('weekday_id');

            $table->primary(['volunteer_id','weekday_id']);

            $table->foreign('volunteer_id')->references('userId')
            ->on('volunteers')->onDelete('cascade');

            $table->foreign('weekday_id')->references('id')
            ->on('weekdays')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('volunteer_weekday');
    }
}
