<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;
use App\Http\Resources\ManufacturerResource;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;

class ManufacturerController extends Controller
{
   /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $manufacturer = ManufacturerResource::collection(Manufacturer::orderBy('name', 'asc')->paginate($request->query('per_page', 5)));

        return inertia('Manufacturer/Index', [
            'items' => $manufacturer
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreManufacturerRequest $request)
    {
        $validatedData = $request->validated();

        $manufacturer = Manufacturer::create($validatedData);

        return response()->json([
            'message' => 'Manufacturer created successfully!',
            'manufacturer' => $manufacturer // Optionally return the created manufacturer data
        ], 201); // 201 Created status code
    }

    /**
     * Display the specified resource.
     */
    public function show(Manufacturer $manufacturer)
    {
        $manufacturer = Manufacturer::findOrFail($manufacturer->id);

        if (!$manufacturer) {
            return redirect()->back()->with('error', 'Manufacturer not found.');
        }

        return response()->json($manufacturer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer)
    {
        $validatedData = $request->validated();

        $manufacturer->update($validatedData);

        return response()->json([
            'message' => 'Manufacturer updated successfully!',
            'manufacturer' => $manufacturer->fresh() // Return the fresh, updated manufacturer data
        ], 200); // 200 OK status code for successful updates
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $manufacturer = Manufacturer::findOrFail($id); // Find the manufacturer or throw a 404 error
            $manufacturer->delete(); // Delete the manufacturer

            return response()->json(['message' => 'Manufacturer deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete manufacturer.'], 500);
        }
    }
}
