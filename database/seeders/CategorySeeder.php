<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['content' => '商品のお届けについて', 'created_at' => now(), 'updated_at' => now()],
            ['content' => '商品の交換について', 'created_at' => now(), 'updated_at' => now()],
            ['content' => '商品トラブル', 'created_at' => now(), 'updated_at' => now()],
            ['content' => 'ショップへのお問い合わせ', 'created_at' => now(), 'updated_at' => now()],
            ['content' => 'その他', 'created_at' => now(), 'updated_at' => now()],
        ];
        Category::insert($categories);
    }
}
