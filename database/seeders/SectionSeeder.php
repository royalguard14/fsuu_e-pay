<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sections = [
            'St. John', 'St. Matthew', 'St. James', 'St. Andrew',
            'St. Michael', 'St. Gabriel', 'St. Peter', 'St. Paul',
            'St. Martha', 'St. Cecilia', 'St. Francis', 'St. Ignatius','Kinder','NurseryII'
        ];

        foreach ($sections as $section) {
            Section::create([
                'section_name' => $section,
            ]);
        }
    }
}
