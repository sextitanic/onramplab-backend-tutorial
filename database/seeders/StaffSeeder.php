<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\StaffLeave;
use App\Models\LeaveApplication;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Staff::truncate();
        StaffLeave::truncate();
        LeaveApplication::truncate();

        // create a staff
        // 新增一名員工
        Staff::factory()
            ->create();

        // create 3 staff with staff leaves
        // 新增三名員工和他們的可休假時數
        Staff::factory()
            ->count(3)
            ->has(
                StaffLeave::factory()
                    ->count(1)
                    ->state(function (array $attributes, Staff $staff) {
                        return ['staff_id' => $staff->id];
                    }),
                'leaveBalance',
            )
            ->create();

        // create a staff who is still in the probation period and named John.
        // 新增一個還在試用期，並且名字為 John 的員工
        Staff::factory([
            'name' => 'John',
        ])
        ->has(
            StaffLeave::factory()
                ->count(1)
                ->inProbationPeriod()
                ->state(function (array $attributes, Staff $staff) {
                    return ['staff_id' => $staff->id];
                }),
            'leaveBalance',
        )
        ->inProbationPeriod()
        ->create();

        // create a staff with staff leaves and a leave application form
        // 新增一個員工，和他的可休假時數與一張請假單
        Staff::factory()
            ->count(1)
            ->has(
                StaffLeave::factory()
                    ->count(1)
                    ->state(function (array $attributes, Staff $staff) {
                        return ['staff_id' => $staff->id];
                    }),
                'leaveBalance',
            )
            ->has(
                LeaveApplication::factory()
                    ->count(1)
                    ->state(function (array $attributes, Staff $staff) {
                        return ['staff_id' => $staff->id];
                    }),
                'leaveApplications',
            )
            ->create();
    }
}
