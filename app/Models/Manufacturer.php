<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class Manufacturer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'url',
        'support_url',
        'support_phone',
        'support_email',
    ];

    /**
     * Get the assets for the manufacturer.
     * Defines a one-to-many relationship: one manufacturer has many assets.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Register a callback to validate the model's attributes before it's created.
        static::creating(function ($manufacturer) {
            $manufacturer->validateAttributes(); // Call our custom validation method
        });

        // Register a callback to validate the model's attributes before it's updated.
        static::updating(function ($manufacturer) {
            $manufacturer->validateAttributes(); // Call our custom validation method
        });
    }

    protected function getValidationRules(): array
    {
        // When creating, the 'name' must be unique.
        // When updating, 'name' must be unique, but ignore the current model's ID.
        $nameRules = 'required|string|max:255';
        if ($this->exists) { // Check if the model already exists (i.e., it's being updated)
            $nameRules .= '|unique:manufacturers,name,' . $this->id;
        } else { // Model is being created
            $nameRules .= '|unique:manufacturers,name';
        }

        return [
            'name' => $nameRules,
            'url' => 'nullable|url|max:255', // URL is optional but must be a valid URL if provided
            'support_url' => 'nullable|url|max:255', // Support URL is optional but must be a valid URL if provided
            'support_phone' => 'required|string|max:20', // Support phone is optional but must be a string with a max length of 20
            'support_email' => 'required|email|max:255', // Support email is optional but must be a valid email if provided
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
