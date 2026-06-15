<?php

namespace Database\Seeders;

use App\Models\Road;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoadSeeder extends Seeder
{
    public function run(): void
    {
        // প্রথমে অ্যাডমিন ইউজার নিন (created_by এর জন্য)
        $admin = User::where('role', 'admin')->first() ?? User::first();

        Road::create([
            'name' => 'মেইন রোড',
            'image_path' => 'roads/main-road.jpg',   // পরে আপলোড করবেন
            'description' => 'চৌধুরী বাড়ির প্রধান রাস্তা',
            'created_by' => $admin->id ?? 1,
        ]);

        Road::create([
            'name' => 'পূর্ব পাড়া রোড',
            'image_path' => 'roads/east-road.jpg',
            'description' => 'পূর্ব দিকের রাস্তা',
            'created_by' => $admin->id ?? 1,
        ]);

        Road::create([
            'name' => 'দক্ষিণ ব্লক',
            'image_path' => null,
            'description' => 'নতুন ব্লকের রাস্তা',
            'created_by' => $admin->id ?? 1,
        ]);
    }
}