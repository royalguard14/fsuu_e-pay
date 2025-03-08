<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'email' => 'admin@zear.com',
            'password' => Hash::make('password'),
            'role_id' => 1, 
            'isDeleted' => false,
            'isActive' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        User::create([
            'username' => 'user',
            'email' => 'user@zear.com',
            'password' => Hash::make('password'),
            'role_id' => 2, 
            'isDeleted' => false,
            'isActive' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
