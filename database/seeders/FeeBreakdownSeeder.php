<?php

namespace Database\Seeders;

use App\Models\FeeBreakdown;
use Illuminate\Database\Seeder;

class FeeBreakdownSeeder extends Seeder
{
    public function run()
    {
        $feeBreakdowns = [
            [
                'academic_year_id' => 1,
                'grade_level_id' => 7,
                'tuition_fee' => 9000,
                'other_fees' => json_encode([
                    'acea_fee' => 10,
                    'ceap_fee' => 25,
                    'computerization_fee' => 265,
                    'dbes_fee' => 240,
                    'english_fee' => 130,
                    'filipino_fee' => 100,
                    'foundation_day_fee' => 150,
                    'id_fee' => 150,
                    'library_fee' => 100,
                    'light_water_fee' => 150,
                    'medical_dental_fee' => 70,
                    'recognition_fee' => 100,
                    'registration_fee' => 50,
                    'science_math_fee' => 150,
                    'science_lab_fee' => 200,
                    'school_publication_fee' => 150,
                    'security_guard_fee' => 650,
                    'socio_cultural_fee' => 20,
                    'sports_fee' => 50,
                    'student_activity_fee' => 230,
                    'welfare_fund_fee' => 50,
                    'support_personnel_fee' => 200,
                    'testing_materials_fee' => 200,
                    'tle_fee' => 100,
                    'safety_housekeeping_sanitation' => 50,
                ]),
            ],
        ];

        foreach ($feeBreakdowns as $feeBreakdown) {
            FeeBreakdown::create($feeBreakdown);
        }
    }
}
