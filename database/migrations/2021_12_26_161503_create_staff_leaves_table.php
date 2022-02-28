<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_leaves', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id')->comment('員工 id，對應 staff 資料表的 id');
            $table->year('year');
            $table->smallInteger('annual')->comment('特休假');
            $table->smallInteger('sick')->comment('病假');
            $table->smallInteger('personal')->comment('事假');
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
        Schema::dropIfExists('staff_leaves');
    }
}
