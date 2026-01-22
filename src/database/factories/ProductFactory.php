<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);
        $slugBase = Str::slug($name);

        return [
            'name' => $name,
            'slug' => $slugBase,
            'sku' => strtoupper($this->faker->unique()->numberBetween(1000, 9999)),
            'description' => $this->faker->optional(0.7)->paragraph(2, true),
            'short_description' => $this->faker->optional(0.8)->sentence(12, true),
            'price' => $this->faker->randomFloat(2, 0, 500),
            'weight' => $this->faker->randomFloat(2, 0, 100),
            'stock' => $this->faker->numberBetween(0, 500),
            'active' => $this->faker->boolean(70),
        ];
    }
}
