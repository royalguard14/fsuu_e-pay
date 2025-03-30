<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Profile;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
 
 
            $this->call([
            RolesTableSeeder::class,
            UsersTableSeeder::class,
            ProfilesTableSeeder::class,
            ModuleSeeder::class,
            SettingSeeder::class,
            GradeLevelSeeder::class,
            SectionSeeder::class,
             AcademicYearSeeder::class,
            
            
        ]);

        User::factory(20)->create()->each(function ($user) {
        Profile::factory()->create(['user_id' => $user->id]);
    });

    }
}
