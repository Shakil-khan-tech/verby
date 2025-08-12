<?php

namespace Database\Factories;

use App\Models\Room;
use Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Room::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => strtoupper(Str::random(1)) . sprintf('%03d', $this->faker->numberBetween(0, 100)),
            'category' => $this->faker->numberBetween(0, 3),
            // 'device_id' => 1,
            'device_id' => \App\Models\Device::all()->random()->id,
        ];
    }
}
