<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountIdentityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_identity', function (Blueprint $table) {
            $table->integer('discount_id');
            $table->integer('identity_id');
            $table->primary(['discount_id','identity_id']);

            $table->foreign('discount_id')->references('id')
            ->on('discounts')->onDelete('cascade');

            $table->foreign('identity_id')->references('id')
            ->on('identities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_identity');
    }
}
