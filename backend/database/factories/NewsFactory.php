<?php

namespace Database\Factories;

use App\Enums\PublishEnum;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::limit($this->faker->paragraph,50,'');
        return [
            'category_id'=>Category::where('type','news')->inRandomOrder()->first()->id,
            'user_id'=>User::inRandomOrder()->first()->id,
            'title'=>$title,
            'slug'=>slug($title),
            'short_description'=>Str::limit($this->faker->paragraph(2),500,''),
            'description'=>$this->faker->paragraph(10),
            'image'=>'/storage/newsface.jpg',
            'publish'=>PublishEnum::PUBLISHED,
        ];
    }
}
