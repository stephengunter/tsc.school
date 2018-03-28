<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCenterDiscountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('center_discount', function (Blueprint $table) {
            $table->integer('center_id');
            $table->integer('discount_id');
            $table->primary(['center_id','discount_id']);

            $table->foreign('center_id')->references('id')
            ->on('centers')->onDelete('cascade');

            $table->foreign('discount_id')->references('id')
            ->on('discounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('center_discount');
    }
}
