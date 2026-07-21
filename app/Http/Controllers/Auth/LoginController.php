<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) return $this->redirectByRole();
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email','password'), $request->boolean('remember'))) {
            $user = Auth::user();
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            // The installed PWA is the student-only app (see auth-login.js) —
            // a valid owner/staff/admin login is still rejected from that
            // context so the installed app never opens anything but a
            // student's own dashboard.
            if ($request->boolean('pwa') && $user->role !== 'student') {
                Auth::logout();
                return back()->withErrors(['email' => 'This app is for students only. Please sign in from a browser instead.'])->withInput();
            }

            return $this->redirectByRole();
        }

        return back()->withErrors(['email' => 'Invalid email or password.'])->withInput();
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    private function redirectByRole()
    {
        return match(Auth::user()->role) {
            'superadmin' => redirect('/admin/dashboard'),
            'owner','staff' => redirect('/owner/dashboard'),
            'student' => redirect('/student/dashboard'),
            default => redirect('/login'),
        };
    }
}
