<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\LocationResource;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $locations = LocationResource::collection(Location::orderBy('name', 'asc')->paginate($request->query('per_page', 5)));

        return inertia('Location/Index', [
            'items' => $locations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLocationRequest $request)
    {
        $validatedData = $request->validated();

        $location = Location::create($validatedData);

        return response()->json([
            'message' => 'Location created successfully!',
            'location' => $location // Optionally return the created location data
        ], 201); // 201 Created status code
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location = Location::findOrFail($location->id);

        if (!$location) {
            return redirect()->back()->with('error', 'Location not found.');
        }

        return response()->json($location);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLocationRequest $request, Location $location)
    {
        $validatedData = $request->validated();

        $location->update($validatedData);

        return response()->json([
            'message' => 'Location updated successfully!',
            'location' => $location->fresh() // Return the fresh, updated location data
        ], 200); // 200 OK status code for successful updates
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $location = Location::findOrFail($id); // Find the location or throw a 404 error
            $location->delete(); // Delete the location

            return response()->json(['message' => 'Location deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete location.'], 500);
        }
    }
}
