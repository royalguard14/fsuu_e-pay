<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Profile;
use App\Models\User;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            Profile::create([
            'user_id' => User::where('username', 'admin')->first()->id,
            'firstname' => 'Zhie',
            'lastname' => 'Bautista',
            'phone_number' => '9277294457',
            'address' => 'Doongan',
            'profile_picture' => 'path/to/pic.jpg',
            'birthdate' => '1993-10-13',
            'gender' => 'Male',
            'nationality' => 'Filipino',
            'bio' => 'Developer.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
