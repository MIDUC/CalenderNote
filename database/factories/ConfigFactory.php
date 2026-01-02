<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Config>
 */
class ConfigFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $configs = [
            'app_name' => 'MySchedule',
            'default_currency' => 'VND',
            'default_language' => 'vi',
            'timezone' => 'Asia/Ho_Chi_Minh',
            'date_format' => 'd/m/Y',
            'time_format' => 'H:i',
        ];
        
        $key = fake()->randomElement(array_keys($configs));
        
        return [
            'config_key' => $key,
            'config_value' => $configs[$key],
            'description' => fake()->optional()->sentence(),
        ];
    }
}
