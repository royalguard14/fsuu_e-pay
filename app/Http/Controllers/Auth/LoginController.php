<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |----------------------------------------------------------------------
    | Login Controller
    |----------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen.
    |
    */

    protected $redirectTo = '/login';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');  // Return the login view
    }

    /**
     * Login a user with either username or email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate login (either email or username) and password
        $request->validate([
            'login' => 'required|string',  // Login field (email or username)
            'password' => 'required|string',  // Password field
        ]);

        // Attempt login with username or email
        if (Auth::attempt(['username' => $request->login, 'password' => $request->password]) ||
            Auth::attempt(['email' => $request->login, 'password' => $request->password])) {
            // Redirect based on the user's role after successful login
            return $this->authenticated($request, Auth::user());
        }

        // If login fails, show an error
        return back()->withErrors([
            'login' => ['The provided credentials are incorrect.'],
        ]);
    }

    /**
     * Logout the user and redirect to the homepage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirect the user after login based on their role.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
protected function authenticated(Request $request, $user)
{


    // Ensure the role relationship is loaded
    if ($user->role && $user->role->role_name) {
        // Redirect based on the role name
        if ($user->role->role_name == 'Developer') {
            return redirect()->route('developer.dashboard');
        }

        if ($user->role->role_name == 'User') {
            return redirect()->route('user.dashboard');
        }
    }

    // If no valid role or role_name, fallback to the home page
    return redirect()->route('home');
}
}
