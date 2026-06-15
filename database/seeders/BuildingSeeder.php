<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Road;
use App\Models\User;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first() ?? User::first();
        $roads = Road::all();

        if ($roads->isEmpty()) {
            $this->command->warn('No roads found. Please run RoadSeeder first.');
            return;
        }

        // প্রথম রোডে কয়েকটা বিল্ডিং
        $firstRoad = $roads->first();

        Building::create([
            'road_id' => $firstRoad->id,
            'name' => 'ব্লক A-১',
            'owner_name' => 'আব্দুল করিম চৌধুরী',
            'total_floors' => 5,
            'total_families' => 12,
            'building_type' => 'Residential',
            'owner_phone' => '01787492561',
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'extra_info' => 'পুরাতন বাড়ি, ভালো অবস্থায় আছে',
            'services' => json_encode(['cleaning', 'security']),
            'image_path' => 'buildings/block-a1.jpg',
            'created_by' => $admin->id ?? 1,
        ]);

        Building::create([
            'road_id' => $firstRoad->id,
            'name' => 'ব্লক B-২',
            'owner_name' => 'রহিম উদ্দিন',
            'total_floors' => 4,
            'total_families' => 8,
            'building_type' => 'Mixed',
            'owner_phone' => '01711112222',
            'latitude' => 23.8110,
            'longitude' => 90.4130,
            'extra_info' => 'নতুন নির্মাণ',
            'services' => json_encode(['cleaning']),
            'image_path' => null,
            'created_by' => $admin->id ?? 1,
        ]);

        // আরও রোডে বিল্ডিং যোগ করতে পারেন...
    }
}