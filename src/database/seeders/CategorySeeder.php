<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roots = Category::factory()->count(8)->create([
            'parent_id' => null,
        ]);

        Category::factory()
            ->count(20)
            ->state(fn () => ['parent_id' => $roots->random()->id])
            ->create();
    }
}
