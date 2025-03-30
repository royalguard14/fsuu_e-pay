<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     DB::table('roles')->insert([
        'role_name' => 'Developer',
        'modules' => json_encode([1,2,3,4,5,6,7,8,9,10,11]),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

     DB::table('roles')->insert([
        'role_name' => 'Admin',
            'modules' => json_encode([5,6,7,8,9,10,11]),  // Empty array for now, can add specific modules later
            'created_at' => now(),
            'updated_at' => now(),
        ]);

     DB::table('roles')->insert([
        'role_name' => 'Cashier',
            'modules' => json_encode([]),  // Empty array for now, can add specific modules later
            'created_at' => now(),
            'updated_at' => now(),
        ]);

     DB::table('roles')->insert([
        'role_name' => 'Student',
            'modules' => json_encode([]),  // Empty array for now, can add specific modules later
            'created_at' => now(),
            'updated_at' => now(),
        ]);

     DB::table('roles')->insert([
        'role_name' => 'Parent',
            'modules' => json_encode([]),  // Empty array for now, can add specific modules later
            'created_at' => now(),
            'updated_at' => now(),
        ]);
 }
}
