<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::where('email', 'test@example.com')->exists()) {
        return;
        }
        // 管理者ユーザーを作成
        User::create([
            'name' => 'Task User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

    }
}
