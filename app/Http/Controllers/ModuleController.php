<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Role;

use Illuminate\Support\Facades\Log;
class ModuleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:Developer'); 
    }

public function index(){
    $modules = Module::all();
    return view('modules.index', compact('modules'));
  
}

public function getModulesForRole(Role $role)
{

    $modules = Module::all();
    $assignedModules = $role->modules;  
    return response()->json([
        'modules' => $modules,  
        'assignedModules' => $assignedModules,  
    ]);
}



public function updateModulesForRole(Request $request)
{
    // Find the role by ID
    $role = Role::findOrFail($request->role_id);
    // Get the updated modules array from the request
    $modules = $request->input('modules', []);
    $modules = array_map('intval', $modules);
    sort($modules);
    $role->modules = $modules;
    $role->save();
    return response()->json(['success' => true]);
}


  // Store new module
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255|unique:modules',
            'icon' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Create and save the module
            $module = Module::create([
                'name' => $request->input('name'),
                'icon' => $request->input('icon'),
                'url' => $request->input('url'),
                'description' => $request->input('description'),
            ]);

            // Redirect back with success message
            return redirect()
                ->route('modules.index')
                ->with([
                    'success' => 'Module created successfully!',
                    'icon' => 'success'
                ]);
        } catch (\Exception $e) {
            // Handle any errors
            return redirect()
                ->back()
                ->withErrors(['error' => 'Failed to create module: ' . $e->getMessage()])
                ->with([
                    'success' => 'Failed to create module',
                    'icon' => 'error'
                ]);
        }
    }




public function update(Request $request, Module $module)
{
    // Validate the input
    $request->validate([
        'name' => 'required|string|max:255|unique:modules,name,' . $module->id,
        'icon' => 'nullable|string|max:255',
        'url' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:1000',
    ]);

    try {
        // Update the module with new data
        $module->update([
            'name' => $request->input('name'),
            'icon' => $request->input('icon'),
            'url' => $request->input('url'),
            'description' => $request->input('description'),
        ]);

        // Redirect back with success message
        return redirect()
            ->route('modules.index')
            ->with([
                'success' => 'Module updated successfully!',
                'icon' => 'success'
            ]);
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withErrors(['error' => 'Failed to update module: ' . $e->getMessage()])
            ->with([
                'success' => 'Failed to update module',
                'icon' => 'error'
            ]);
    }
}




public function destroy(Module $module)
{
    try {
        // Delete the module
        $module->delete();

        return redirect()
            ->route('modules.index')
            ->with([
                'success' => 'Module deleted successfully!',
                'icon' => 'success'
            ]);
    } catch (\Exception $e) {
        return redirect()
            ->back()
            ->withErrors(['error' => 'Failed to delete module: ' . $e->getMessage()])
            ->with([
                'success' => 'Failed to delete module',
                'icon' => 'error'
            ]);
    }
}





}
