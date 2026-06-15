<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // অ্যাডমিন ইউজার
        User::create([
            'name' => 'Sajid Chowdhury',
            'email' => 'sajid@gmail.com',
            'phone' => '01787492561',
            'address' => 'Dhaka, Bangladesh',
            'role' => 'admin',
            'is_active' => true,
            'password' => Hash::make('password123'), // আপনার পাসওয়ার্ড
        ]);

        // আরও কয়েকটা টেস্ট ইউজার
        User::create([
            'name' => 'Rahim Uddin',
            'email' => 'rahim@example.com',
            'phone' => '01711112222',
            'address' => 'Chittagong',
            'role' => 'user',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Karim Mia',
            'email' => 'karim@example.com',
            'phone' => '01733334444',
            'address' => 'Sylhet',
            'role' => 'moderator',
            'is_active' => true,
            'password' => Hash::make('password123'),
        ]);

        // আরও ডাটা চাইলে এভাবে যোগ করুন...
    }
}