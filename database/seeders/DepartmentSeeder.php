<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departments')->truncate();

        $now = now()->toDateTimeString();
        $data = [
            ['name' => 'HR', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Customer', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Adminstration', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Develop', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('departments')->insert($data);
    }
}
