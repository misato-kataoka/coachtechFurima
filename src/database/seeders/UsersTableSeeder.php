<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'username'  => '鈴木　一郎',
            'email'     => 'testuser@example.com',
            'password'  => Hash::make('password123'),
            'email_verified_at' => now(),
            'profile_pic'=>'profiles/E3lD5ZJ1AiDxebllSUPQs9U1udoNtpufwcWVZnyY.png',
            'post_code' => '123-4567',
            'address'   => '東京都サンプル区1-1-1',
            'building'  => 'テストビル101',
        ]);

        User::create([
            'username'  => '山田　花子',
            'email'     => 'sampleuser@example.com',
            'password'  => Hash::make('password456'),
            'email_verified_at' => now(),
            'profile_pic' => 'profiles/usagi.png',
            'post_code' => '987-6543',
            'address'   => '大阪府サンプル市2-2-2',
            'building'  => 'サンプルマンション202',
        ]);

        User::create([
            'username'  => '田中　次郎',
            'email'     => 'tanakajirou@example.com',
            'password'  => Hash::make('password789'),
            'email_verified_at' => now(),
            'profile_pic' => null,
            'post_code' => '567-8910',
            'address'   => '京都府サンプル市サンプル町3-3',
            'building'  => null,
        ]);
    }
}
