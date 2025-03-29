<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\GradeLevel;
use App\Models\Section;
use App\Models\EnrollmentHistory;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnrollmentHistoryController extends Controller
{
    public function index()
{

    $currentYear = AcademicYear::where('current', true)->first();

    if (!$currentYear) {
        return redirect()->back()->with([
            'success' => 'No active academic year found',
            'icon' => 'error'
        ]);
    }



    // Students already enrolled in the current academic year
  $enrolledStudents = EnrollmentHistory::with(['user.profile', 'gradeLevel', 'section'])
    ->where('academic_year_id', $currentYear->id)
    ->get();



    // Students with role_id = 4 (students) who are NOT yet enrolled
    $unenrolledStudents = User::with('profile')
        ->where('role_id', 4)
        ->whereDoesntHave('enrollmentHistories', function ($query) use ($currentYear) {
            $query->where('academic_year_id', $currentYear->id);
        })
        ->get();


$gradeLevels = GradeLevel::all();
$sections = Section::all()->keyBy('id'); // Index sections by their ID

$gradeLevelsWithSections = $gradeLevels->map(function ($grade) use ($sections) {
    // Decode only if section_ids is a JSON string
    $sectionIds = is_string($grade->section_ids)
        ? json_decode($grade->section_ids, true) // Decode from JSON string
        : $grade->section_ids;

    // Check if decoding failed or it's not an array
    if (!is_array($sectionIds)) {
        $sectionIds = []; // Default to an empty array if something’s wrong
    }

    // Get section names based on the section IDs
    $sectionNames = collect($sectionIds)->map(function ($id) use ($sections) {
        return $sections[$id]->section_name ?? 'N/A'; // Find section name or fallback
    });

    $grade->section_names = $sectionNames->toArray(); // Attach section names
    return $grade;
});







     return view('enrollees.index', compact('enrolledStudents', 'unenrolledStudents', 'currentYear', 'gradeLevels', 'sections'));
}

public function getSections($gradeId)
{
    $grade = GradeLevel::find($gradeId);
    $sectionIds = $grade->section_ids;
    $sections = Section::whereIn('id', $sectionIds)->get();


    return response()->json(['sections' => $sections]);
}

public function enroll(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'grade_level_id' => 'required|exists:grade_levels,id',
        'section_id' => 'required|exists:sections,id',
    ]);

    $currentYear = AcademicYear::where('current', true)->first();

    if (!$currentYear) {
        return redirect()->route('enrollees.index')->with([
            'success' => 'No active academic year found',
            'icon' => 'error'
        ]);
    }

    $currentIdAY = $currentYear->id; // ✅ Correct academic year ID

    // ✅ Check if the user is already enrolled in this academic year
    $alreadyEnrolled = EnrollmentHistory::where('user_id', $request->user_id)
        ->where('academic_year_id', $currentIdAY) // ✅ Corrected this line
        ->exists();

    if ($alreadyEnrolled) {
        return redirect()->back()->with([
            'icon' => 'error',
            'success' => 'This student is already enrolled for the selected academic year.'
        ]);
    }

    // ✅ Prevent double-click enrollment (disable button in frontend)
    EnrollmentHistory::create([
        'user_id' => $request->user_id,
        'grade_level_id' => $request->grade_level_id,
        'section_id' => $request->section_id,
        'academic_year_id' => $currentIdAY,
        'enrollment_date' => now(),
    ]);

    return redirect()->back()->with([
        'icon' => 'success',
        'success' => 'Student successfully enrolled!'
    ]);
}





public function transfer(Request $request) {
    $request->validate([
        'enrollment_id' => 'required|exists:enrollment_histories,id',
        'section_id' => 'required|exists:sections,id',
    ]);

    $enrollment = EnrollmentHistory::findOrFail($request->enrollment_id);
    $enrollment->update([
        'section_id' => $request->section_id
    ]);

    return redirect()->back()->with([
        'icon' => 'success',
        'success' => 'Student successfully transferred!'
    ]);
}






}
