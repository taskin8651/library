<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            'slug'         => 'required|string|max:50|unique:libraries,slug|alpha_dash',
            'email'        => 'required|email|unique:users,email',
            'phone'        => 'required|digits:10',
            'password'     => 'required|min:8|confirmed',
            'plan_id'      => 'required|exists:plans,id',
        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // Create library
        $library = Library::create([
            'name'           => $request->library_name,
            'slug'           => strtolower($request->slug),
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
