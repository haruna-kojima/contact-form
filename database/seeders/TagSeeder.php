<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => '質問', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '要望', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '不具合報告', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'ご意見', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'その他', 'created_at' => now(), 'updated_at' => now()],
        ];
        Tag::insert($tags);
    }

    
}
