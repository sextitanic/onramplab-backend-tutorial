<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();
            $table->integer('staff_id')->comment('員工 id，對應 staff 資料表的 id');
            $table->date('application_date')->comment('申請日期');
            $table->string('leave_type', 45)->comment('請假類型');
            $table->date('start_leave_date')->comment('請假開始日期');
            $table->date('end_leave_date')->comment('請假結束日期');
            $table->smallInteger('leave_hours')->comment('請假時數');
            $table->tinyInteger('status')->default(0)->comment('0: 待審核, 1: 審核通過, 2: 不通過, 3: 取消');
            $table->string('approver', 45)->comment('審核人員')->nullable();
            $table->date('approval_date')->comment('審核日期')->nullable();
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
        Schema::dropIfExists('leave_applications');
    }
}
