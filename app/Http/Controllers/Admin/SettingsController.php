<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::current();
        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = Setting::current();

        $request->validate([
            'site_name'         => 'required|string|max:100',
            'meta_title'        => 'nullable|string|max:255',
            'meta_description'  => 'nullable|string|max:500',
            'meta_keywords'     => 'nullable|string|max:255',
            'contact_email'     => 'nullable|email|max:150',
            'contact_phone'     => 'nullable|string|max:20',
            'facebook_url'      => 'nullable|url|max:255',
            'twitter_url'       => 'nullable|url|max:255',
            'instagram_url'     => 'nullable|url|max:255',
            'linkedin_url'      => 'nullable|url|max:255',
            'youtube_url'       => 'nullable|url|max:255',
            'logo'              => 'nullable|image|max:4024',
            'favicon'           => 'nullable|image|max:4024',
            'og_image'          => 'nullable|image|max:4048',
        ]);

        $data = $request->only([
            'site_name', 'meta_title', 'meta_description', 'meta_keywords',
            'contact_email', 'contact_phone',
            'facebook_url', 'twitter_url', 'instagram_url', 'linkedin_url', 'youtube_url',
        ]);

        foreach (['logo', 'favicon', 'og_image'] as $field) {
            if ($request->hasFile($field)) {
                $data[$field] = $request->file($field)->store('site', 'public');
            }
        }

        $settings->update($data);

        return back()->with('success', 'Website settings updated successfully.');
    }
}
