<?php

namespace Database\Seeders;

use App\Models\Staff;
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

        // create a staff
        // 新增一名員工
        Staff::factory()
            ->create();

        // create 3 staff
        // 新增三名員工
        Staff::factory()
            ->count(3)
            ->create();

        // create a staff who is still in the probation period and named John.
        // 新增一個還在試用期，並且名字為 John 的員工
        Staff::factory([
            'name' => 'John',
            'is_probation' => 1,
            'join_date' => date('Y-m-d', strtotime('-60 days')),
        ])
        ->create();
    }
}
