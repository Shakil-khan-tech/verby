<?php

namespace Database\Factories;

use App\Models\Record;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Record::class;

    // $time = new DateTime();
    // protected $time = new DateTime();
    // protected $work_time = $this->time->modify("-5 days");
    // protected $pause_time = $work_time;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // 'employee_id' => $employee_id,
            'device_id' => 1,
            // 'action' => $this->faker->numberBetween(0, 3),
            // 'perform' => $this->faker->numberBetween(0, 3),
            'identity' => $this->faker->numberBetween(0, 2),
            // 'time' => $this->work_time->modify("+6 hours"),
        ];
    }
}
