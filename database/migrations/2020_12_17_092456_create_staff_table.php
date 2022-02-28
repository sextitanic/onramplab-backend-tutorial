<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 255)->unique();
            $table->tinyInteger('gender')->comment('性別，1: 男性, 2: 女性, 3: LGBT');
            $table->bigInteger('department_id')->comment('關聯 depatments 資料表的 id 值');
            $table->boolean('is_probation')->comment('是否還在試用期，0: 否, 1: 是');
            $table->date('join_date')->comment('到職日期');
            $table->date('leave_date')->comment('離職日期')->nullable();
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
        Schema::dropIfExists('staff');
    }
}
