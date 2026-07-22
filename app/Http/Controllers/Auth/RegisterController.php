<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RegisterController extends Controller
{
    public function showRegister()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('auth.register', compact('plans'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'library_name' => 'required|string|max:100',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|digits:10',
            'password'     => 'required|min:8|confirmed',
            'plan_id'      => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // The library's QR check-in URL (/checkin/{slug}) just needs a unique,
        // URL-safe value — there's no reason to make the owner type one at
        // sign-up, so it's derived from the library name instead.
        $baseSlug = Str::slug($request->library_name) ?: 'library';
        $slug = $baseSlug;
        $suffix = 1;
        while (Library::where('slug', $slug)->exists()) {
            $suffix++;
            $slug = $baseSlug . '-' . $suffix;
        }

        // Create library
        $library = Library::create([
            'name'           => $request->library_name,
            'slug'           => $slug,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'plan_id'        => $plan->id,
            'status'         => 'active',
            'trial_ends_at'  => Carbon::now()->addDays($plan->trial_days),
            'plan_expires_at'=> Carbon::now()->addDays($plan->trial_days),
        ]);

        // Create owner user
        $user = User::create([
            'library_id' => $library->id,
            'name'       => $request->library_name . ' Owner',
            'email'      => $request->email,
            'phone'      => $request->phone,
            'password'   => Hash::make($request->password),
            'role'       => 'owner',
            'is_active'  => true,
        ]);

        Auth::login($user);
        return redirect('/owner/dashboard')->with('success', 'Welcome! Your ' . $plan->trial_days . '-day free trial has started.');
    }
}
