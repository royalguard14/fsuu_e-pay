<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeLevel;

class GradeLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
$grades = [
    ['level' => 'Kinder', 'section_ids' => [13]],
    ['level' => 'Nursery 2', 'section_ids' => [14]],
    ['level' => 'Grade 7', 'section_ids' => [1, 2]],
    ['level' => 'Grade 8', 'section_ids' => [3, 4]],
    ['level' => 'Grade 9', 'section_ids' => [5, 6]],
    ['level' => 'Grade 10', 'section_ids' => [7, 8]],
    ['level' => 'Grade 11', 'section_ids' => [9, 10]],
    ['level' => 'Grade 12', 'section_ids' => [11, 12]],
];


        foreach ($grades as $grade) {
            GradeLevel::create([
                'level' => $grade['level'],
                'section_ids' => $grade['section_ids'], // This will be stored as JSON
            ]);
        }
    }
}
