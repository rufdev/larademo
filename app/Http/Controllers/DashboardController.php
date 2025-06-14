<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use App\Models\Asset;
use App\Models\Manufacturer;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $categories = CategoryResource::collection(Category::orderBy('name', 'asc')->paginate($request->query('per_page', 5)));


        return inertia('Dashboard', [
            'items' => $categories,
        ]);
    }

    public function stats()
    {
        $totalAssets = Asset::count();
        $totalCategories = Category::count();
        $totalManufacturers = Manufacturer::count();
        $totalLocations = Location::count();
        $totalUsers = User::count();

        $assetsByStatus = Asset::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $assetsByCategory = Asset::select('category_id', DB::raw('count(*) as total'))
            ->groupBy('category_id')
            ->with('category:id,name') // Include category name
            ->get();

        $assetsByLocation = Asset::select('location_id', DB::raw('count(*) as total'))
            ->groupBy('location_id')
            ->with('location:id,name') // Include location name
            ->get();

        $assetsByAssignedUser = Asset::select('assigned_to_user_id', DB::raw('count(*) as total'))
            ->groupBy('assigned_to_user_id')
            ->with('assignedTo:id,name') // Include user name
            ->get();

        return response()->json([
            'totals' => [
                'total_assets' => $totalAssets,
                'total_categories' => $totalCategories,
                'total_manufacturers' => $totalManufacturers,
                'total_locations' => $totalLocations,
                'total_users' => $totalUsers,
            ],
            'charts' => [
                'assets_by_status' => $assetsByStatus,
                'assets_by_category' => $assetsByCategory,
                'assets_by_location' => $assetsByLocation,
                'assets_by_assigned_user' => $assetsByAssignedUser,
            ],
        ]);
        return;
    }
}
