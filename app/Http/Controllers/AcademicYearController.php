<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;


class AcademicYearController extends Controller
{
    public function index()
    {
        $years = AcademicYear::all();
        return view('academic_years.index', compact('years'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start' => 'required|integer|digits:4|min:' . now()->year . '|unique:academic_years,start',
        ]);
        if ($validator->fails()) {
            return redirect()->route('academic.index')->withErrors($validator)->with([
                'success' => 'Failed to create academic year',
                'icon' => 'error'
            ]);
        }
        $endYear = $request->start + 1;
        AcademicYear::create([
            'start' => $request->start,
            'end' => $endYear,
        'current' => false // Always default to false
    ]);
        return redirect()->route('academic.index')->with([
            'success' => 'Academic year created successfully',
            'icon' => 'success'
        ]);
    }




 public function setCurrent($id)
{
    try {
        // Unset current for all academic years
        AcademicYear::query()->update(['current' => false]);

        // Set current for the selected year
        $year = AcademicYear::findOrFail($id);
        $year->current = true;
        $year->save();

        return response()->json([
            'success' => 'Academic year set as current successfully!',
            'icon' => 'success'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to set current academic year.',
            'icon' => 'error'
        ]);
    }
}


public function destroy(AcademicYear $academicYear)
{
    if ($academicYear->current) {
        return redirect()->route('academic.index')->with([
            'success' => 'The current active academic year cannot be deleted.',
            'icon' => 'error'
        ]);
    }

    $academicYear->delete();

    return redirect()->route('academic.index')->with([
        'success' => 'Academic year deleted successfully',
        'icon' => 'success'
    ]);
}
}
