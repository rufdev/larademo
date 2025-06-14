<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Inserts sample location data into the 'locations' table.
     */
    public function run(): void
    {
        Location::create(['name' => 'Main Office - Room 101', 'address' => '123 Tech Blvd, Suite 101']);
        Location::create(['name' => 'Main Office - Room 205', 'address' => '123 Tech Blvd, Suite 205']);
        Location::create(['name' => 'Server Room A', 'address' => 'Basement, Main Office']);
        Location::create(['name' => 'Warehouse', 'address' => '456 Storage Ln, Industrial Park']);
    }
}