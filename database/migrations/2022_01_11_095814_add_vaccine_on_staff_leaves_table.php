<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVaccineOnStaffLeavesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff_leaves', function (Blueprint $table) {
            $table->smallInteger('vaccine')
                ->comment('疫苗假')
                ->after('unpaid_sick')
                ->default(8);
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
            $table->dropColumn('vaccine');
        });
    }
}
