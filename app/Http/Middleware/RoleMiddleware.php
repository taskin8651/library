<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        // Check if library is active (for owners/staff/students)
        if (in_array($user->role, ['owner','staff','student'])) {
            $library = $user->library;

            // A suspended library is blocked outright — this is independent of
            // trial/expiry state, since isOnTrial() only looks at trial_ends_at
            // and would otherwise let a suspended-but-still-in-trial account
            // keep working as if nothing happened.
            if ($library && $library->status === 'suspended') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                abort(403, 'This library account has been suspended. Please contact support.');
            }

            if ($library && !$library->isActive() && !$library->isOnTrial()) {
                if ($user->role === 'owner' && !$request->is('owner/subscription/*')) {
                    return redirect('/owner/subscription/plans')
                        ->with('warning', 'Your subscription has expired. Please renew to continue.');
                } elseif ($user->role !== 'owner') {
                    abort(403, 'This library\'s subscription has expired. Please contact the library owner.');
                }
            }
        }

        return $next($request);
    }
}
