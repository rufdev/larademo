<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator; 

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Get the assets for the category.
     * Defines a one-to-many relationship: one category has many assets.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    // --- Model Events for Internal Data Integrity Validation ---

    /**
     * Boot method to add model events.
     * This method is called once per model, allowing you to register listeners
     * for various Eloquent events.
     */
    protected static function boot()
    {
        parent::boot();

        // Register a callback to validate the model's attributes before it's created.
        static::creating(function ($category) {
            $category->validateAttributes(); // Call our custom validation method
        });

        // Register a callback to validate the model's attributes before it's updated.
        static::updating(function ($category) {
            $category->validateAttributes(); // Call our custom validation method
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
        // When creating, the 'name' must be unique.
        // When updating, 'name' must be unique, but ignore the current model's ID.
        $nameRules = 'required|string|max:255';
        if ($this->exists) { // Check if the model already exists (i.e., it's being updated)
            $nameRules .= '|unique:categories,name,' . $this->id;
        } else { // Model is being created
            $nameRules .= '|unique:categories,name';
        }

        return [
            'name' => $nameRules,
            'description' => 'nullable|string|max:500',
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