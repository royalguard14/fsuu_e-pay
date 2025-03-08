<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    // Display list of settings
    public function index()
    {
        $settings = Setting::orderBy('type')->get();
        return view('settings.index', compact('settings'));
    }

    // Show form to create a new setting
    public function create()
    {
        return view('settings.create');
    }

    // Store new setting in the database
    public function store(Request $request)
    {
        $request->validate([
            'function_desc' => 'required|string|max:255|unique:settings,function_desc',
            'function' => 'required|string|max:255',
            'type' => 'required|in:frontend,backend',
        ]);

        Setting::create([
            'function_desc' => $request->function_desc,
            'function' => $request->function,
            'type' => $request->type,
        ]);

        return redirect()->route('settings.index')->with([
            'success' => 'Setting created successfully!',
            'icon' => 'success'
        ]);
    }

    // Show edit form for a specific setting
    public function edit(Setting $setting)
    {
        return view('settings.edit', compact('setting'));
    }

    // Update setting in the database
    public function update(Request $request, Setting $setting)
    {
        $request->validate([
            'function_desc' => 'required|string|max:255|unique:settings,function_desc,' . $setting->id,
            'function' => 'required|string|max:255',
            'type' => 'required|in:frontend,backend',
        ]);

        $setting->update([
            'function_desc' => $request->function_desc,
            'function' => $request->function,
            'type' => $request->type,
        ]);

        return redirect()->route('settings.index')->with([
            'success' => 'Setting updated successfully!',
            'icon' => 'success'
        ]);
    }

    // Delete a setting
    public function destroy(Setting $setting)
    {
        $setting->delete();
        return redirect()->route('settings.index')->with([
            'success' => 'Setting deleted successfully!',
            'icon' => 'success'
        ]);
    }
}
