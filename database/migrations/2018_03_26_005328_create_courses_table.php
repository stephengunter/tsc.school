<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('termId')->unsigned(); 
            $table->integer('centerId')->unsigned(); 
            $table->integer('teacherGroupId')->unsigned()->nullable(); 
            $table->integer('categoryId')->unsigned(); 

            $table->string('name');
            $table->string('level')->nullable(); 
            $table->string('number')->nullable(); 
            
            $table->boolean('net')->default(true);
		
			$table->date('beginDate')->nullable();
            $table->date('endDate')->nullable();
            $table->integer('weeks')->unsigned()->nullable();
            $table->integer('hours')->unsigned()->nullable();
            	
            
            $table->decimal('tuition', 8, 2)->nullable();   //學費
            $table->decimal('cost', 8, 2)->nullable();  //材料費
            $table->text('materials')->nullable();   //材料    槌子,榔頭,電鑽
            $table->text('description')->nullable();
            $table->text('target')->nullable();  //招生對象

            $table->text('caution')->nullable();  // 注意事項

            $table->date('openDate')->nullable();    //開始報名
            $table->date('closeDate')->nullable();   //截止報名
            $table->integer('limit')->nullable();    //人數上限 
            $table->integer('min')->default(0);    //人數下限

            $table->boolean('discount')->default(false);

            $table->integer('importance')->default(0);

            $table->boolean('reviewed')->default(false);
            $table->boolean('active')->default(true);            
            $table->boolean('removed')->default(false);
            
            $table->integer('updatedBy')->unsigned()->nullable();
            $table->integer('reviewedBy')->unsigned()->nullable();

            $table->string('ps')->nullable(); 

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
        Schema::dropIfExists('courses');
    }
}
