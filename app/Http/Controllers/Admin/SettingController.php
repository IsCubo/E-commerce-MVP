<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(SettingRequest $request)
    {
        $data = $request->validated();

        // Handle specific keys
        $keys = ['brand_name', 'whatsapp_number', 'welcome_message'];

        foreach ($keys as $key) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $data[$key] ?? '']
            );
        }

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('branding', 'public');
            
            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'logo_path')->first();
            if ($oldLogo && $oldLogo->value) {
                Storage::disk('public')->delete($oldLogo->value);
            }

            Setting::updateOrCreate(
                ['key' => 'logo_path'],
                ['value' => $path]
            );
        }

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
