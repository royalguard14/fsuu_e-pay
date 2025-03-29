<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the current year
        $currentYear = now()->year;
        $nextYear = $currentYear + 1;

        // Store only the years
        AcademicYear::updateOrCreate(
            ['start' => $currentYear, 'end' => $nextYear],
            ['current' => true]
        );
    }
}
