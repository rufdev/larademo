<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Manufacturer;
use App\Models\User; // Import the User model
use App\Enums\AssetStatusEnum; // Import AssetStatusEnum
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Inserts sample asset data into the 'assets' table, linking to other seeded data.
     */
    public function run(): void
    {
        // Ensure dependent data exists (from other seeders)
        $dell = Manufacturer::where('name', 'Dell')->first();
        $hp = Manufacturer::where('name', 'HP')->first();
        $cisco = Manufacturer::where('name', 'Cisco')->first();

        $laptopCategory = Category::where('name', 'Laptop')->first();
        $monitorCategory = Category::where('name', 'Monitor')->first();
        $networkCategory = Category::where('name', 'Network Device')->first();

        $room101 = Location::where('name', 'Main Office - Room 101')->first();
        $serverRoom = Location::where('name', 'Server Room A')->first();
        $warehouse = Location::where('name', 'Warehouse')->first();

        $managerUser = User::where('email', 'manager@example.com')->first();
        $basicUser = User::where('email', 'user@example.com')->first();

        // Create Sample Assets
        Asset::create([
            'asset_tag' => 'LAPTOP-001',
            'name' => 'Dell Latitude 7420',
            'serial_number' => 'DLAT7420S001',
            'model_name' => 'Latitude 7420',
            'purchase_date' => '2023-01-15',
            'purchase_price' => 1200.00,
            'status' => AssetStatusEnum::Deployed, // Using the Enum
            'notes' => 'Assigned to Manager User.',
            'category_id' => $laptopCategory->id,
            'location_id' => $room101->id,
            'manufacturer_id' => $dell->id,
            'assigned_to_user_id' => $managerUser->id,
        ]);

        Asset::create([
            'asset_tag' => 'MONITOR-001',
            'name' => 'Dell UltraSharp U2721DE',
            'serial_number' => 'DULTRA2721S001',
            'model_name' => 'U2721DE',
            'purchase_date' => '2023-02-01',
            'purchase_price' => 350.00,
            'status' => AssetStatusEnum::Deployed,
            'notes' => 'Paired with LAPTOP-001.',
            'category_id' => $monitorCategory->id,
            'location_id' => $room101->id,
            'manufacturer_id' => $dell->id,
            'assigned_to_user_id' => $managerUser->id,
        ]);

        Asset::create([
            'asset_tag' => 'SWITCH-SR-001',
            'name' => 'Cisco Catalyst 2960X',
            'serial_number' => 'CSWC2960S001',
            'model_name' => 'WS-C2960X-24TS-L',
            'purchase_date' => '2022-05-10',
            'purchase_price' => 2500.00,
            'status' => AssetStatusEnum::InStorage,
            'notes' => 'New switch, awaiting deployment.',
            'category_id' => $networkCategory->id,
            'location_id' => $warehouse->id, // Currently in warehouse
            'manufacturer_id' => $cisco->id,
            'assigned_to_user_id' => null, // Not assigned to a user
        ]);

        Asset::create([
            'asset_tag' => 'LAPTOP-002',
            'name' => 'HP EliteBook 840 G8',
            'serial_number' => 'HPELITE840S002',
            'model_name' => 'EliteBook 840 G8',
            'purchase_date' => '2023-03-20',
            'purchase_price' => 1100.00,
            'status' => AssetStatusEnum::InStorage,
            'notes' => 'Freshly received, ready for new user.',
            'category_id' => $laptopCategory->id,
            'location_id' => $warehouse->id,
            'manufacturer_id' => $hp->id,
            'assigned_to_user_id' => null,
        ]);

        Asset::create([
            'asset_tag' => 'LAPTOP-003',
            'name' => 'Dell XPS 13',
            'serial_number' => 'DXPS13S003',
            'model_name' => 'XPS 13 9300',
            'purchase_date' => '2022-01-01',
            'purchase_price' => 900.00,
            'status' => AssetStatusEnum::Maintenance,
            'notes' => 'Screen replacement needed.',
            'category_id' => $laptopCategory->id,
            'location_id' => $room101->id,
            'manufacturer_id' => $dell->id,
            'assigned_to_user_id' => $basicUser->id,
        ]);
    }
}