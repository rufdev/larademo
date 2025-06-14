<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class Location extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
    ];

    /**
     * Get the assets at this location.
     * Defines a one-to-many relationship: one location has many assets.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Register a callback to validate the model's attributes before it's created.
        static::creating(function ($location) {
            $location->validateAttributes(); // Call our custom validation method
        });

        // Register a callback to validate the model's attributes before it's updated.
        static::updating(function ($location) {
            $location->validateAttributes(); // Call our custom validation method
        });
    }

    protected function getValidationRules(): array
    {
        // When creating, the 'name' must be unique.
        // When updating, 'name' must be unique, but ignore the current model's ID.
        $nameRules = 'required|string|max:255';
        if ($this->exists) { // Check if the model already exists (i.e., it's being updated)
            $nameRules .= '|unique:locations,name,' . $this->id;
        } else { // Model is being created
            $nameRules .= '|unique:locations,name';
        }

        return [
            'name' => $nameRules,
            'address' => 'required|string|max:500',
        ];
    }

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
