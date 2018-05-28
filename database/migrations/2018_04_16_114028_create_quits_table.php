<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quits', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('signupId')->unsigned();
			$table->foreign('signupId')->references('id')->on('signups')->onDelete('cascade');

            $table->date('date');
            $table->decimal('tuitions', 8, 2)->default(0)->nullable();
            $table->decimal('fee', 8, 2)->default(0)->nullable();

            $table->integer('paywayId');
            $table->integer('status')->default(-1); 

            $table->string('account_bank')->nullable();
            $table->string('account_branch')->nullable();
            $table->string('account_owner')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_code')->nullable();

            $table->boolean('special')->default(false); //有特殊原因
            $table->boolean('auto')->default(false); //系統自動產生的
            $table->integer('tranId')->unsigned()->nullable();
            
            $table->string('ps')->nullable();
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
        Schema::dropIfExists('quits');
    }
}
