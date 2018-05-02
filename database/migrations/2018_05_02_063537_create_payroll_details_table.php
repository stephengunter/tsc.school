<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayrollDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrollDetails', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('payrollId')->unsigned();
            $table->foreign('payrollId')->references('id')->on('payrolls')->onDelete('cascade');

            $table->integer('lessonId')->unsigned();
            $table->date('date');
            $table->integer('on');
            $table->integer('off');
            $table->integer('minutes');
            $table->integer('studentCount');
            $table->string('wageName');
            $table->decimal('wageMoney', 8, 2)->nullable();

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
        Schema::dropIfExists('payrollDetails');
    }
}
