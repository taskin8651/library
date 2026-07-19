<?php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function edit()
    {
        $library = Auth::user()->library;
        return view('owner.settings.edit', compact('library'));
    }

    public function update(Request $request)
    {
        $library = Auth::user()->library;

        $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|max:150',
            'phone'       => 'required|digits:10',
            'address'     => 'nullable|string|max:255',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'tagline'     => 'nullable|string|max:150',
            'theme_color' => 'nullable|string|max:7',
            'logo'        => 'nullable|image|max:2048',
            'stamp'       => 'nullable|image|max:2048',
            'banner'      => 'nullable|image|max:4096',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'address', 'city', 'state', 'tagline', 'theme_color']);

        foreach (['logo', 'stamp', 'banner'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('library', 'public');
            }
        }

        $library->update($data);

        return back()->with('success', 'Library settings updated successfully.');
    }
}
