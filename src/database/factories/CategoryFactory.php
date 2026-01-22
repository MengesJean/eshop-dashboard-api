<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->word();
        $slugBase = Str::slug($name);
        return [
            'name' => ucfirst($name),
            'slug' => $slugBase,
            'description' => $this->faker->optional(0.7)->paragraph(),
            'active' => $this->faker->boolean(85),
            'level' => $this->faker->numberBetween(0, 50),
            'parent_id' => null,
        ];
    }
}
