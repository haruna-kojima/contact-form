<?php

namespace Database\Factories;

use App\Models\Contact;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::inRandomOrder()->first()?->id ?? Category::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'gender' => $this->faker->randomElement([1, 2, 3]), // 1:男性, 2:女性, 3:その他
            'email' => $this->faker->unique()->safeEmail(),
            'tel' => '09012345678', // バリデーションを通過する正しい形式の番号
            'address' => $this->faker->address(),
            'building' => $this->faker->secondaryAddress(), // 任意項目
            'detail' => 'これはテストのお問い合わせ内容詳細です。',
        ];
    }
}
