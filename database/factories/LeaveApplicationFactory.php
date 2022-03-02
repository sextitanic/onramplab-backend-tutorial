<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LeaveApplication;

class LeaveApplicationFactory extends Factory
{
    protected $model = LeaveApplication::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'application_date' => date('Y-m-d H:i:s'),
            'leave_type' => 'annual',
            'start_leave_date' => date('Y-m-d', strtotime(date('Y-m-d') . ' +2 days')),
            'end_leave_date' => date('Y-m-d', strtotime(date('Y-m-d') . ' +2 days')),
            'leave_hours' => 8,
        ];
    }
}
