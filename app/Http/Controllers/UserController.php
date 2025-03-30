<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
public function index()
{
    // Get the ID of the currently logged-in user
    $loggedInUserId = Auth::id();

    // Fetch users excluding the currently logged-in user
    $users = User::with('role', 'profile')->where('id', '!=', $loggedInUserId)->get();

    // Fetch all roles
    $roles = Role::all();

    // Return the view with users and roles
    return view('users.index', compact('users', 'roles'));
}


    public function store(Request $request)
    {



        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);


        return redirect()->route('users.index')->with([
            'success' => 'User created successfully',
            'icon' => 'success'
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user->update([
            'username' => $request->username,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'isActive' => $request->has('isActive'),
        ]);

        return redirect()->route('users.index')->with([
            'success' => 'User updated successfully',
            'icon' => 'success'
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with([
            'success' => 'User deleted successfully',
            'icon' => 'success'
        ]);
    }


    public function toggleActive($id, Request $request)
{
    $user = User::findOrFail($id);
    $user->isActive = $request->isActive;
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'User status updated successfully.'
    ]);
}



 public function storeS(Request $request)
    {
        $request->validate([
            'lrn' => 'required|unique:profiles',
            'firstname' => 'required',
            'lastname' => 'required',
            'phone_number' => 'nullable|string',
            'birthdate' => 'nullable|date',
            'gender' => 'required|in:Male,Female,Other',
            'nationality' => 'nullable|string',
            'address' => 'nullable|string',
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'profile_picture' => 'nullable|image|max:2048',
        ]);

        // Create user account
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 4,  // Assuming 4 is the student role
        ]);

        // Handle profile picture upload
        $profilePicturePath = null;
        if ($request->hasFile('profile_picture')) {
            $profilePicturePath = $request->file('profile_picture')->store('profile_pictures', 'public');
        }

        // Create profile
        Profile::create([
            'user_id' => $user->id,
            'lrn' => $request->lrn,
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'phone_number' => $request->phone_number,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'nationality' => $request->nationality,
            'address' => $request->address,
            'profile_picture' => $profilePicturePath,
        ]);

        return redirect()->route('users.student')->with('success', 'Student account created successfully!');
    }



        public function createS()
    {
        return view('users.student');  // Ensure this matches the view filename
    }


}