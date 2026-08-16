<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            // 50文字以内のユニークな単語をタグ名として生成
            'name' => $this->faker->unique()->word(),
        ];
    }
}