<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
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
            'phone_no' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'city_name' => $this->faker->city(),
            'zone_name' => $this->faker->country
        ];
    }
}
