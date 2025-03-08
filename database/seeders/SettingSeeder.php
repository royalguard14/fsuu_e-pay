<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'function_desc' => 'System Title',
                'function' => 'ZDS',
                'type' => 'backend',
            ],
            [
                'function_desc' => 'Maintenance Mode',
                'function' => '0',  
                'type' => 'frontend',
            ],
            [
                'function_desc' => 'Allow User Registration',
                'function' => '1',  
                'type' => 'frontend',
            ],
            [
                'function_desc' => 'Default User Role',
                'function' => '1',
                'type' => 'backend',
            ],
            [
                'function_desc' => 'Admin Email Notifications',
                'function' => '1',  
                'type' => 'backend',
            ],
            [
                'function_desc' => 'API Access',
                'function' => '1',  
                'type' => 'backend',
            ],

        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['function_desc' => $setting['function_desc']],
                $setting
            );
        }
    }
}
