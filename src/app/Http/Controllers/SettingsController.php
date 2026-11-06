<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\AuditService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable|string',
        ]);

        $updated = 0;

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if (! $setting || $setting->value === $value) {
                continue;
            }

            $old = $setting->toArray();
            $setting->update(['value' => $value]);
            app(AuditService::class)->logUpdate($setting, $old);
            $updated++;
        }

        return redirect('/settings')->with('success', $updated . ' setting(s) updated successfully.');
    }
}
