<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

  Module::create([
    'name' => 'Roles',
    'icon' => 'fa-users',
    'description' => 'Manage roles within the system and assign permissions to users.',
    'url' => 'roles.index'  
]);

Module::create([
    'name' => 'Users',
    'icon' => 'fa-users',
    'description' => 'Manage individual users, their details, and roles within the system.',
    'url' => 'users.index'  
]);

Module::create([
    'name' => 'Modules',
    'icon' => 'fa-cogs',
    'description' => 'Configure and manage system modules and their settings.',
    'url' => 'modules.index'  
]);

Module::create([
    'name' => 'Settings',
    'icon' => 'fa-cogs',
    'description' => 'Manage global system settings, including configurations and preferences.',
    'url' => 'settings.index'  
]);

Module::create([
    'name' => 'Grade',
    'icon' => 'fa-user-tag',
    'description' => 'Manage Grade Level',
    'url' => 'grade.index'  
]);

Module::create([
    'name' => 'Sections',
    'icon' => 'fa-school',
    'description' => 'Manage Sections',
    'url' => 'section.index'  
]);


Module::create([
    'name' => 'Academic Year',
    'icon' => 'fa-calendar-alt',
    'description' => 'Manage Academic Year of the school',
    'url' => 'academic.index'  
]);

Module::create([
    'name' => 'Enrollees',
    'icon' => 'fa-users',
    'description' => 'Manage Enrollees',
    'url' => 'enrollees.index'  
]);

Module::create([
    'name' => 'GCash',
    'icon' => 'fa-mobile-alt',
    'description' => 'Manage Gcash Account',
    'url' => 'gcash.index'  
]);


Module::create([
    'name' => 'Payment Breakdown',
    'icon' => 'fa-piggy-bank',
    'description' => 'Manage Gcash Account',
    'url' => 'fees.index'  
]);

Module::create([
    'name' => 'Students',
    'icon' => 'fa-users',
    'description' => 'Manage Student Account',
    'url' => 'users.student'  
]);

    }
}
