<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SiteSettingController extends Controller
{
    /**
     * Show the Navigation & Footer edit form (singleton).
     */
    public function edit()
    {
        $settings = SiteSetting::cached();
        return view('admin.settings.edit', compact('settings'));
    }

    /**
     * Update navigation + footer settings (logo, nav color, social links, address).
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nav_color'       => ['nullable', 'string', 'max:20'],
            'whatsapp_link'   => ['nullable', 'string', 'max:255'],
            'facebook_link'   => ['nullable', 'string', 'max:255'],
            'youtube_link'    => ['nullable', 'string', 'max:255'],
            'footer_address'  => ['nullable', 'string', 'max:500'],
            'logo'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:2048'],
            'remove_logo'     => ['nullable', 'boolean'],
        ]);

        $settings = SiteSetting::cached();

        $settings->nav_color      = $validated['nav_color'] ?: null;
        $settings->whatsapp_link  = $validated['whatsapp_link'] ?: null;
        $settings->facebook_link  = $validated['facebook_link'] ?: null;
        $settings->youtube_link   = $validated['youtube_link'] ?: null;
        $settings->footer_address = $validated['footer_address'] ?: null;

        // Handle logo removal
        if ($request->boolean('remove_logo') && $settings->logo_path) {
            $absolute = public_path($settings->logo_path);
            if (File::exists($absolute)) {
                File::delete($absolute);
            }
            $settings->logo_path = null;
        }

        // Handle new logo upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $dir = public_path('uploads/site');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true);
            }

            // Remove old file if replacing
            if ($settings->logo_path) {
                $oldAbsolute = public_path($settings->logo_path);
                if (File::exists($oldAbsolute)) {
                    File::delete($oldAbsolute);
                }
            }

            $ext      = $file->getClientOriginalExtension();
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $ext;
            $file->move($dir, $filename);

            $settings->logo_path = 'uploads/site/' . $filename;
        }

        $settings->save();

        return redirect()->route('admin.settings.edit')
            ->with('status', 'Navigation & Footer settings updated successfully.');
    }
}
