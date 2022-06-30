<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() : array
    {
        return [
            'name' => [$this->faker->locale => $this->faker->name],
            'description' => [$this->faker->locale => $this->faker->text]
        ];
    }
}
