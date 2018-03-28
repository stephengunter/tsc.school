<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIdentityUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('identity_user', function (Blueprint $table) {
            $table->integer('identity_id');
            $table->integer('user_id');
            $table->primary(['identity_id','user_id']);

            $table->foreign('identity_id')->references('id')
            ->on('identities')->onDelete('cascade');

            $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('identity_user');
    }
}
