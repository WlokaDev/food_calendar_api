<?php

namespace Database\Factories;

use App\Enums\ShoppingListStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShoppingListFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'status' => ShoppingListStatusEnum::ACTIVE->value
        ];
    }
}
