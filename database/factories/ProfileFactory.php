<?php

namespace Database\Factories;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'lrn' => $this->faker->optional()->numerify('##########'),
            'user_id' => User::factory(), // Automatically create a user if one isn’t provided
            'firstname' => $this->faker->firstName,
            'lastname' => $this->faker->lastName,
            'phone_number' => $this->faker->optional()->phoneNumber,
            'address' => $this->faker->optional()->address,
            'profile_picture' => $this->faker->optional()->imageUrl(100, 100, 'people'),
            'birthdate' => $this->faker->optional()->date,
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'nationality' => $this->faker->optional()->randomElement(['Filipino', 'Pakistani', 'American']),
            'bio' => $this->faker->optional()->sentence,
        ];
    }
}
