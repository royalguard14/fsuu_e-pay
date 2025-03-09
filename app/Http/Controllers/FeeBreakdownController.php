<?php

namespace App\Http\Controllers;

use App\Models\FeeBreakdown;
use App\Models\AcademicYear;
use App\Models\GradeLevel;
use App\Models\EnrollmentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeeBreakdownController extends Controller
{
public function index()
{
    $feeBreakdowns = FeeBreakdown::with('gradeLevel', 'academicYear')->get();
    $currentYear = AcademicYear::where('current', true)->first();
    $academicYears = AcademicYear::all();
    $gradeLevels = GradeLevel::all();

    // Get the grade levels that already have a fee breakdown for the current year
    $usedGradeLevels = FeeBreakdown::where('academic_year_id', $currentYear->id)
        ->pluck('grade_level_id')
        ->toArray();





    return view('paybreak.index', compact('feeBreakdowns', 'academicYears', 'gradeLevels', 'currentYear', 'usedGradeLevels'));
}

public function store(Request $request)
{
    $exists = FeeBreakdown::where('academic_year_id', $request->academic_year_id)
        ->where('grade_level_id', $request->grade_level_id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Fee breakdown for this academic year and grade level already exists.');
    }

    FeeBreakdown::create([
        'academic_year_id' => $request->academic_year_id,
        'grade_level_id' => $request->grade_level_id,
        'tuition_fee' => $request->tuition_fee,
        'other_fees' => json_encode($request->other_fees)
    ]);

    return redirect()->back()->with('success', 'Fee breakdown added successfully.');
}






    public function update(Request $request, $id)
{
    $fee = FeeBreakdown::findOrFail($id);

    $fee->update([
        'tuition_fee' => $request->tuition_fee,
        'other_fees' => json_encode($request->other_fees),
    ]);

    return redirect()->route('fees.index')->with('success', 'Fee breakdown updated successfully!');
}


  public function destroy($id)
{
    $fee = FeeBreakdown::findOrFail($id);

    // Check if there are any enrollments using this grade level and academic year
    $hasEnrollments = EnrollmentHistory::where('grade_level_id', $fee->grade_level_id)
        ->where('academic_year_id', $fee->academic_year_id)
        ->exists();

    if ($hasEnrollments) {
        return redirect()->back()->with([
            'icon' => 'error',
            'success' => 'Cannot delete this fee breakdown because there are existing enrollments for this grade level and academic year.'
        ]);
    }

    $fee->delete();

    return redirect()->back()->with([
        'icon' => 'success',
        'success' => 'Fee breakdown deleted successfully.'
    ]);
}

}
