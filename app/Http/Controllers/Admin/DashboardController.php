<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\User;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [
            'total_libraries'  => Library::count(),
            'active_libraries' => Library::where('status','active')->count(),
            'pending_libraries'=> Library::where('status','pending')->count(),
            'total_revenue'    => Subscription::where('status','active')->sum('amount'),
            'recent_libraries' => Library::with('plan')->latest()->take(10)->get(),
            'plans'            => Plan::withCount('libraries')->get(),
        ];
        return view('admin.dashboard', $data);
    }

    public function libraries(Request $request)
    {
        $libraries = Library::with('plan')
            ->when($request->search, fn($q) => $q->where('name','like',"%{$request->search}%")
                ->orWhere('email','like',"%{$request->search}%"))
            ->when($request->status, fn($q) => $q->where('status',$request->status))
            ->latest()->paginate(20);

        return view('admin.libraries.index', compact('libraries'));
    }

    public function approveLibrary(Library $library)
    {
        $library->update(['status' => 'active']);
        return back()->with('success', $library->name . ' approved successfully.');
    }

    public function suspendLibrary(Library $library)
    {
        $library->update(['status' => 'suspended']);
        return back()->with('success', $library->name . ' suspended.');
    }

    public function plans()
    {
        $plans = Plan::withCount('libraries')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
        ]);
        $plan->update($request->only(['name','description','price','max_branches','staff_accounts','white_label','is_active']));
        return back()->with('success', 'Plan updated successfully.');
    }
}
