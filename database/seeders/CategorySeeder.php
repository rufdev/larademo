<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Inserts sample category data into the 'categories' table.
     */
    public function run(): void
    {
        Category::create(['name' => 'Laptop', 'description' => 'Portable computers']);
        Category::create(['name' => 'Desktop PC', 'description' => 'Stationary computers']);
        Category::create(['name' => 'Monitor', 'description' => 'Display devices']);
        Category::create(['name' => 'Printer', 'description' => 'Devices for printing documents']);
        Category::create(['name' => 'Network Device', 'description' => 'Routers, switches, access points']);
        Category::create(['name' => 'Server', 'description' => 'Dedicated computing devices']);
        Category::create(['name' => 'Software License', 'description' => 'Licenses for software products']);
    }
}
