<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\Setting;
use App\Providers\AppServiceProvider;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

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
            $path = $this->imageUploadService->store($request->file('logo'), 'branding');

            // Delete old logo if exists
            $oldLogo = Setting::where('key', 'logo_path')->first();
            if ($oldLogo) {
                $this->imageUploadService->delete($oldLogo->value);
            }

            Setting::updateOrCreate(
                ['key' => 'logo_path'],
                ['value' => $path]
            );
        }

        // Los settings quedan cacheados "para siempre" (ver AppServiceProvider)
        // porque se leen en cada vista; hay que invalidarlos explícitamente
        // cada vez que se guardan cambios acá.
        Cache::forget(AppServiceProvider::SETTINGS_CACHE_KEY);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
