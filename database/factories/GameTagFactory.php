<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class GameTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tag_name' => $this->faker->unique()->word,
        ];
    }
}

