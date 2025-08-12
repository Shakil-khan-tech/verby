<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmployeeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // true or false
            'PartTime' => $this->faker->boolean(),
            'function' => $this->faker->numberBetween(0, 3),
            'noqnaSmena' => $this->faker->boolean(),
            'name' => $this->faker->name(),
            'surname' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'DOB' => $this->faker->dateTimeBetween('1990-01-01', '2012-12-31')->format('d/m/Y'),
            'gender' => $this->faker->boolean(),
            'maried' => $this->faker->boolean(),
            'strasse' => $this->faker->streetAddress(),
            'PLZ' => $this->faker->postcode(),
            'ORT1' => $this->faker->city(),
            'ORT' => $this->faker->city(),
            'AHV' => $this->faker->numerify('756.###.#.###.##'),
            'bankname' => $this->faker->company(),
            'IBAN' => $this->faker->iban(),
            'TAX' => $this->faker->numerify('756.###.#.###.##'),
            'rroga' => $this->faker->numerify('###'),
            'EhChf' => $this->faker->numerify('###'),
            'decki200' => $this->faker->numberBetween(0, 100),
            'decki250' => $this->faker->numberBetween(0, 100),
            'BVG' => $this->faker->numberBetween(0, 100),
            'start' => $this->faker->dateTimeBetween(Carbon::now()->startOfYear(), Carbon::yesterday()),
            'end' => null,
            'Perqind1' => $this->faker->randomFloat(2, 0, 9),
            'Perqind2' => $this->faker->randomFloat(2, 0, 9),
            'Perqind3' => $this->faker->randomFloat(2, 0, 9),
            'pin' => $this->faker->numerify('####'),
            'card' => $this->faker->numerify('####'),
            'camera' => null,
            'sage_number' => $this->faker->numerify('###'),
            'api_monitoring' => $this->faker->boolean(),
            'work_percetage' => $this->faker->randomFloat(2, 0, 9),
            'insurance_6_1' => $this->faker->boolean(),
            'insurance_6_2' => $this->faker->randomFloat(2, 0, 9),
            'insurance_6_3' => $this->faker->randomFloat(2, 0, 9),
            'insurance_6_4' => $this->faker->randomFloat(2, 0, 9),
            'insurance_6_5' => $this->faker->boolean(),
            'insurance_7_1' => $this->faker->text(),
            'insurance_15_1' => $this->faker->boolean(),
            'insurance_15_2' => $this->faker->boolean(),
            'insurance_15_3' => $this->faker->numerify('756.###.#.###.##'),
            'insurance_15_4' => $this->faker->boolean(),
            'insurance_15_5' => $this->faker->numerify('756.###.#.###.##'),
            'insurance_15_6' => $this->faker->numerify('756.###.#.###.##'),
            'insurance_15_7' => $this->faker->numerify('756.###.#.###.##'),
            'insurance_16_1' => $this->faker->text(),
        ];
    }
}
