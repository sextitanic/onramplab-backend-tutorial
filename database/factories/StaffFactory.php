<?php

namespace Database\Factories;

use App\Models\Staff;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffFactory extends Factory
{
    protected $model = Staff::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $birthday = $this->faker->dateTimeBetween('-45 years', '-25 years');

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail,
            'gender' => rand(1, 100) > 30 ? 1 : 2,
            'birthday' => $birthday->format('Y-m-d'),
            'birth_month' => $birthday->format('m'),
            'department_id' => rand(1, 4),
            'is_probation' => 0,
            'join_date' => $this->faker->dateTimeBetween('-2 years', '-100 days')->format('Y-m-d'),
        ];
    }

    public function inProbationPeriod()
    {
        return $this->state([
            'is_probation' => 1,
            'join_date' => $this->faker->dateTimeBetween('-90 days', 'now')->format('Y-m-d'),
        ]);
    }
}
