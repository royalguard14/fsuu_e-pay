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
              
               [
                'function_desc' => 'Mission',
                'function' => '1. Cathechism: to know Christ better and the teachings of the church
2. Stewardship: to become humble and obient of the church
3. Student-Centered: the student (individual and as a group) as the center of the school mission.
4. Collaborative Learning: a collaborative relationship between students and teachers students and students',  
'type' => 'frontend',
            ],
             [
                'function_desc' => 'Vision',
                'function' => 'Christ-centered person who care for the earth and fellow human beings',  
                'type' => 'frontend',
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
