<?php

namespace Database\Factories;

use App\Enums\UserRoleEnum; // Import the UserRoleEnum
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Define roles using the Enum values
        $roles = [
            UserRoleEnum::SuperAdmin,
            UserRoleEnum::InventoryManager,
            UserRoleEnum::InventoryUser,
        ];

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => $this->faker->randomElement($roles), // Assign a random role from the enum
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Define a state for a Super Admin user.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRoleEnum::SuperAdmin,
        ]);
    }

    /**
     * Define a state for an Inventory Manager user.
     */
    public function inventoryManager(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRoleEnum::InventoryManager,
        ]);
    }

    /**
     * Define a state for an Inventory User.
     */
    public function inventoryUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRoleEnum::InventoryUser,
        ]);
    }
}
