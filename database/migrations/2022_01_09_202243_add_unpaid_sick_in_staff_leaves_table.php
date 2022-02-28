<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnpaidSickInStaffLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->smallInteger('unpaid_sick')
                ->comment('無支薪病假')
                ->after('sick')
                ->default(40);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->dropColumn('unpaid_sick');
        });
    }
}
