<?php

namespace App\Http\Controllers;

use App\Models\GradeLevel;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GradeLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:Developer,Admin');

    }



public function index()
{
    $grades = GradeLevel::all();
    $sections = Section::all(); // Fetch all sections
    return view('grade.index', compact('grades', 'sections'));
}


public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'grade_name' => 'required|unique:grade_levels,level|max:255',
    ]);

    if ($validator->fails()) {
        return redirect()->route('grade.index')->withErrors($validator)->with([
            'success' => 'Failed to create grade level',
            'icon' => 'error'
        ]);
    }

    try {
        GradeLevel::create(['level' => $request->grade_name, 'section_ids' => []]);

        return redirect()->route('grade.index')->with([
            'success' => 'Grade level created successfully',
            'icon' => 'success'
        ]);
    } catch (\Exception $e) {
        return redirect()->route('grade.index')->with([
            'success' => 'Failed to create grade level: ' . $e->getMessage(),
            'icon' => 'error'
        ]);
    }
}


public function update(Request $request, GradeLevel $gradeLevel)
{
    $validator = Validator::make($request->all(), [
        'level' => 'required|unique:grade_levels,level,' . $gradeLevel->id . '|max:255',
    ]);

    if ($validator->fails()) {
        return redirect()->route('grade.index')->withErrors($validator)->with([
            'success' => 'Failed to update grade level',
            'icon' => 'error'
        ]);
    }

    try {
        $gradeLevel->update(['level' => $request->level]); // <-- Updated here

        return redirect()->route('grade.index')->with([
            'success' => 'Grade level updated successfully',
            'icon' => 'success'
        ]);
    } catch (\Exception $e) {
        return redirect()->route('grade.index')->with([
            'success' => 'Failed to update grade level: ' . $e->getMessage(),
            'icon' => 'error'
        ]);
    }
}



    public function destroy(GradeLevel $gradeLevel)
    {
        try {
            $gradeLevel->delete();

            return redirect()->route('grade.index')->with([
                'success' => 'Grade level deleted successfully',
                'icon' => 'success'
            ]);
        } catch (\Exception $e) {
            return redirect()->route('grade.index')->with([
                'success' => 'Failed to delete grade level: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

public function getSectionsForGrade(GradeLevel $gradeLevel)
{
    // Get all section IDs currently assigned to any grade
    $assignedSectionIds = GradeLevel::whereNotNull('section_ids')
        ->pluck('section_ids')
        ->flatten()
        ->unique()
        ->toArray();

    // Get sections not already assigned, OR currently assigned to this grade
    $sections = Section::whereNotIn('id', $assignedSectionIds)
        ->orWhereIn('id', $gradeLevel->section_ids ?? [])
        ->get();

    $assignedSections = $gradeLevel->section_ids ?? [];

    return response()->json([
        'sections' => $sections,
        'assignedSections' => $assignedSections,
    ]);
}


public function updateSectionsForGrade(Request $request, GradeLevel $gradeLevel)
{
    $sections = $gradeLevel->section_ids ?? []; // Get current section IDs

    $sectionId = (int) $request->input('section_id'); // The section being toggled

    if (in_array($sectionId, $sections)) {
        // If section already exists, remove it
        $sections = array_diff($sections, [$sectionId]);
    } else {
        // If section doesn’t exist, add it
        $sections[] = $sectionId;
    }

    sort($sections); // Optional: keep it tidy

    $gradeLevel->section_ids = array_values($sections); // Re-index and save
    $gradeLevel->save();

    return response()->json(['success' => true, 'section_ids' => $sections]);
}






}
