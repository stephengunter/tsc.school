<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bankPosts', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->string('from');
            $table->decimal('amount', 8, 2);
            $table->integer('serial');
            $table->string('code');  //虛擬帳號
            $table->dateTime('payAt');  //繳款日期時間
            $table->text('text');  
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
        Schema::dropIfExists('bankPosts');
    }
}
