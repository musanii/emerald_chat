<?php

namespace Database\Factories;

use App\Models\Channel;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->word();
        return [
            'department_id' => Department::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'type' => $this->faker->randomElement(['public', 'private']),
            'description' => $this->faker->sentence(),
        ];
    }
}
