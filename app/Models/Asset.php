<?php

namespace App\Models;

use App\Enums\AssetStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Asset extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'location_id',
        'manufacturer_id',
        'assigned_to_user_id',
        'asset_tag',
        'name',
        'serial_number',
        'model_name',
        'purchase_date',
        'purchase_price',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     * Defines how certain attributes should be cast when retrieved from the database.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'status' => AssetStatusEnum::class, // Assuming you might create an Enum for Asset Status later
    ];

    /**
     * Get the category that owns the asset.
     * Defines a many-to-one relationship: many assets belong to one category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the location that the asset is assigned to.
     * Defines a many-to-one relationship: many assets belong to one location.
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get the manufacturer of the asset.
     * Defines a many-to-one relationship: many assets belong to one manufacturer.
     */
    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    /**
     * Get the user that the asset is assigned to.
     * Defines a many-to-one relationship: many assets can be assigned to one user.
     * Uses 'assigned_to_user_id' as the foreign key and 'users' table.
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Boot the model and register validation callbacks for creating and updating.
     */
    protected static function boot()
    {
        parent::boot();

        // Register a callback to validate the model's attributes before it's created.
        static::creating(function ($asset) {
            $asset->validateAttributes(); // Call our custom validation method
        });

        // Register a callback to validate the model's attributes before it's updated.
        static::updating(function ($asset) {
            $asset->validateAttributes(); // Call our custom validation method
        });
    }

    /**
     * Defines the validation rules for the model's attributes.
     * These rules ensure the integrity of the data being saved to the database,
     * regardless of the source (e.g., HTTP request, console command, seeder).
     *
     * @return array
     */
    protected function getValidationRules(): array
    {
        // Validation rules for the 'name' and 'serial_number' fields
        $nameRules = 'required|string|max:255';
        $serialNumberRules = 'required|string|max:255';

        if ($this->exists) { // Check if the model already exists (i.e., it's being updated)
            $nameRules .= '|unique:assets,name,' . $this->id;
            $serialNumberRules .= '|unique:assets,serial_number,' . $this->id;
        } else { // Model is being created
            $nameRules .= '|unique:assets,name';
            $serialNumberRules .= '|unique:assets,serial_number';
        }

        return [
            'name' => $nameRules,
            'serial_number' => $serialNumberRules,
            'description' => 'nullable|string|max:500',
            'purchase_date' => 'nullable|date', // Ensure the purchase date is a valid date
        ];
    }

    /**
     * Validate the model's attributes against the defined rules.
     *
     * @throws ValidationException If validation fails, it throws an exception.
     * This is an internal check, primarily for data integrity.
     */
    public function validateAttributes(): void
    {
        $validator = Validator::make($this->attributes, $this->getValidationRules());

        if ($validator->fails()) {
            // Throwing a ValidationException here ensures that invalid data
            // cannot be persisted to the database.
            throw new ValidationException($validator);
        }
    }


}
