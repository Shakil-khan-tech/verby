<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DeviceSeeder extends Seeder
{
    protected $needle = 0;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $devices = [
          'Dorint Zurich', 'St Gothardt', 'Hilton', 'Büro', 'Dorint Basel', 'Hyperion Basel', 'H4 Hotel Solothurn', 'Hotel Euler', 'H Plus', 'Kameha', 'Hotel Dormero'
        ];

        \App\Models\User::factory()
            ->count(10)
            ->state(new Sequence(
              ['name' => 'Device 1', 'email' => 'device_1@verby.ch', 'is_device' => 1],
              ['name' => 'Device 2', 'email' => 'device_2@verby.ch', 'is_device' => 1],
              ['name' => 'Device 3', 'email' => 'device_3@verby.ch', 'is_device' => 1],
              ['name' => 'Device 4', 'email' => 'device_4@verby.ch', 'is_device' => 1],
              ['name' => 'Device 5', 'email' => 'device_5@verby.ch', 'is_device' => 1],
              ['name' => 'Device 6', 'email' => 'device_6@verby.ch', 'is_device' => 1],
              ['name' => 'Device 7', 'email' => 'device_7@verby.ch', 'is_device' => 1],
              ['name' => 'Device 8', 'email' => 'device_8@verby.ch', 'is_device' => 1],
              ['name' => 'Device 9', 'email' => 'device_9@verby.ch', 'is_device' => 1],
              ['name' => 'Device 10', 'email' => 'device_10@verby.ch', 'is_device' => 1],
          ))
          ->create()
          ->each(function ($user) use ($devices) {
            \App\Models\Device::factory()->create([
                'name' => $devices[ $this->needle ],
                'user_id' => $user->id
            ]);
            $this->needle++;
          });
    }
}
