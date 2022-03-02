<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Staff;
use App\Models\StaffLeave;

class StaffLeaveFactory extends Factory
{
    protected $model = StaffLeave::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'year' => date('Y'),
            'annual' => 80,
            'sick' => 40,
            'unpaid_sick' => 40,
            'vaccine' => 8,
            'personal' => 80,
        ];
    }

    public function inProbationPeriod()
    {
        return $this->state([
            'year' => date('Y'),
            'annual' => 0,
            'sick' => 0,
            'unpaid_sick' => 40,
            'vaccine' => 0,
            'personal' => 80,
        ]);
    }
}
