<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    // Ensure only admin can access the role management
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('checkRole:Developer'); 
    }







    public function index()
    {
        // Get all roles
        $roles = Role::all();

        return view('roles.index', compact('roles'));
    }



public function store(Request $request)
{
    // Manual validation
    $validator = Validator::make($request->all(), [
        'role_name' => 'required|unique:roles|max:255',
    ]);

    if ($validator->fails()) {
        return redirect()
            ->route('roles.index')
            ->withErrors($validator)  // Send validation errors to the view
            ->with([
                'success' => 'Failed to create role',  // Custom message
                'icon' => 'error'
            ]);
    }

    try {
        // Create a new role
        Role::create([
            'role_name' => $request->role_name,
            'modules' => [],
        ]);

        return redirect()
            ->route('roles.index')
            ->with([
                'success' => 'Role created successfully',
                'icon' => 'success'
            ]);
        
    } catch (\Exception $e) {
        return redirect()
            ->route('roles.index')
            ->with([
                'success' => 'Failed to create role: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
    }
}

public function update(Request $request, Role $role)
{
    // Manual validation
    $validator = Validator::make($request->all(), [
        'name' => 'required|unique:roles,role_name,' . $role->id . '|max:255', // Validate 'name' field
    ]);

    if ($validator->fails()) {
        return redirect()
            ->route('roles.index')
            ->withErrors($validator)
            ->with([
                'success' => 'Failed to update role',
                'icon' => 'error'
            ]);
    }

    try {
        // Update the role
        $role->update([
            'role_name' => $request->name,  // Ensure 'name' is coming from the form input
        ]);

        return redirect()
            ->route('roles.index')
            ->with([
                'success' => 'Role updated successfully',
                'icon' => 'success'
            ]);

    } catch (\Exception $e) {
        return redirect()
            ->route('roles.index')
            ->with([
                'success' => 'Failed to update role: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
    }
}



public function destroy(Role $role)
{
    try {
        // Delete the role
        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with([
                'success' => 'Role deleted successfully',
                'icon' => 'success'
            ]);

    } catch (\Exception $e) {
        return redirect()
            ->route('roles.index')
            ->with([
                'success' => 'Failed to delete role: ' . $e->getMessage(),
                'icon' => 'error'
            ]);
    }
}
}
