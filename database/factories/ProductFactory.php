<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'name' => $this->faker->name(),
            'name_mm' => $this->faker->unique()->md5,
            'price' => $this->faker->numberBetween(100, 2000),
            'image' => $this->faker->imageUrl(),
            'weight' => $this->faker->numerify('## kg'),
            'is_available' => $this->faker->boolean(80),
        ];
    }
}
