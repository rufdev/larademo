<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Asset;
use App\Models\Manufacturer;
use App\Models\Category;
use App\Models\Location;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call users first, as assets can be assigned to them
        $this->call(UserSeeder::class);

        // Call manufacturers, categories, and locations next, as assets depend on them
        $this->call(ManufacturerSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(LocationSeeder::class);

        // Finally, call the AssetSeeder, as it depends on all the above
        $this->call(AssetSeeder::class);

        // Create 20 more random users (beyond the 3 specific ones)
        User::factory(20)->create();

        // Create 10 more random manufacturers
        Manufacturer::factory(10)->create();

        // Create 10 more random categories
        Category::factory(10)->create();

        // Create 10 more random locations
        Location::factory(10)->create();

        // Create 100 random assets
        // These assets will randomly pick existing categories, locations, manufacturers, and users
        // (including the specific ones from the UserSeeder and the random ones from UserFactory)
        Asset::factory(100)->create();
    }
}
