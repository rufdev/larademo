<?php

namespace Database\Seeders;

use App\Models\Manufacturer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Inserts sample manufacturer data into the 'manufacturers' table.
     */
    public function run(): void
    {
        Manufacturer::create(['name' => 'Dell', 'url' => 'https://www.dell.com']);
        Manufacturer::create(['name' => 'HP', 'url' => 'https://www.hp.com']);
        Manufacturer::create(['name' => 'Lenovo', 'url' => 'https://www.lenovo.com']);
        Manufacturer::create(['name' => 'Cisco', 'url' => 'https://www.cisco.com']);
        Manufacturer::create(['name' => 'Samsung', 'url' => 'https://www.samsung.com']);
        Manufacturer::create(['name' => 'Apple', 'url' => 'https://www.apple.com']);
    }
}