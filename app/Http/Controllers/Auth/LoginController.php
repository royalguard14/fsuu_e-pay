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
    // Validate login input (either email or username)
    $request->validate([
        'login' => 'required|string',  // Login field (email or username)
        'password' => 'required|string',  // Password field
    ]);

    // Find the user by username or email
    $user = \App\Models\User::where('username', $request->login)
        ->orWhere('email', $request->login)
        ->first();

    // Check if the user exists
    if (!$user) {
        return back()->withErrors(['login' => 'The provided credentials are incorrect.']);
    }

    // Check if the user is active
    if (!$user->isActive) {
        return back()->withErrors(['login' => 'Your account is inactive. Please contact the administrator.']);
    }

    // Attempt login with username or email
    if (Auth::attempt(['username' => $request->login, 'password' => $request->password]) ||
        Auth::attempt(['email' => $request->login, 'password' => $request->password])) {
        
        // Redirect based on the user's role after successful login
        return $this->authenticated($request, Auth::user());
    }

    // If login fails, show an error
    return back()->withErrors(['login' => 'The provided credentials are incorrect.']);
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
        return redirect()->route('home');    }
    /**
     * Redirect the user after login based on their role.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user->role && $user->role->role_name) {
            if ($user->role->role_name == 'Developer') {
                return redirect()->route('dashboard.developer');
            }
            if ($user->role->role_name == 'Admin') {
                return redirect()->route('dashboard.admin');
            }
            if ($user->role->role_name == 'Cashier') {
                return redirect()->route('payment.cashier');
            }
            if ($user->role->role_name == 'Student') {
                return redirect()->route('payment.student');
            }
            if ($user->role->role_name == 'Parent') {
                return redirect()->route('error');
            }
        }
    
        return redirect()->route('home');
    }
}