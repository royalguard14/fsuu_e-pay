<?php

namespace App\Http\Controllers;

use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::all();
        return view('section.index', compact('sections'));
    }

    public function store(Request $request)
    {


        $request->validate([
            'section_name' => 'required|unique:sections,section_name',
        ]);

        Section::create([
            'section_name' => $request->section_name,
        ]);

        return redirect()->route('section.index')->with('success', 'Section created successfully!');
    }

  public function update(Request $request)
{
    $request->validate([
        'section_name' => 'required|unique:sections,section_name,' . $request->section_id,
    ]);

    $section = Section::findOrFail($request->section_id);

    $section->update([
        'section_name' => $request->section_name,
    ]);

    return redirect()->route('section.index')->with('success', 'Section updated successfully!');
}


    public function destroy(Section $section)
    {
        $section->delete();
        return redirect()->route('section.index')->with('success', 'Section deleted successfully!');
    }
}
