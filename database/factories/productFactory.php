<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\product>
 */
class productFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);

        $subCategories  = [4,5,6];
        $subCatRandKey  = array_rand($subCategories);

        $brands  = [1,2,7,8,9,10,11,12,13];
        $brandRandKey  = array_rand($brands);
        return [
          'title' => $title,
          'description' => fake()->paragraph(5),
         'slug' => $slug,
          'category_id' => 8,
         'sub_category_id' => $subCategories[$subCatRandKey],
          'brand_id' => $brands[$brandRandKey],
          'price' => fake()->randomFloat(2, 1, 100),
          'sku' => rand(1000,100000),
          'track_qty' => 'Yes',
          'qty' => fake()->numberBetween(1, 100),
          'is_featured' => 'Yes',
        ];
    }
}
