<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $groups = ['general', 'contact', 'footer', 'social'];
        $settings = [];

        foreach ($groups as $group) {
            $settings[$group] = Setting::where('group', $group)
                ->orderBy('order')
                ->get();
        }

        return view('admin.settings.index', compact('settings', 'groups'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                // Handle boolean type
                if ($setting->type === 'boolean') {
                    $value = $value === 'on' ? '1' : '0';
                }

                // Handle image upload
                if ($setting->type === 'image' && $request->hasFile("settings.{$key}")) {
                    $file = $request->file("settings.{$key}");
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/settings'), $filename);
                    $value = 'uploads/settings/' . $filename;
                }

                Setting::set($key, $value);
            }
        }

        // Clear all settings cache
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings updated successfully!');
    }
}
