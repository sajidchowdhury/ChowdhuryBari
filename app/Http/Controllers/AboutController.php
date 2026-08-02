<?php

namespace App\Http\Controllers;

use App\Models\AboutInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AboutController extends Controller
{
    /**
     * Show the About Us edit form (singleton).
     */
    public function edit()
    {
        $about = AboutInfo::current();
        return view('admin.about.edit', compact('about'));
    }

    /**
     * Update the About Us content (headline + image + short description).
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'headline'    => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'], // 5MB
            'remove_image' => ['nullable', 'boolean'],
        ]);

        $about = AboutInfo::current();

        $about->headline = $validated['headline'];
        $about->description = $validated['description'];

        // Handle image removal
        if ($request->boolean('remove_image') && $about->image_path) {
            $absolute = public_path($about->image_path);
            if (File::exists($absolute)) {
                File::delete($absolute);
            }
            $about->image_path = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            $file = $request->file('image');

            $dir = public_path('uploads/about');
            if (!File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true);
            }

            // Remove old file if replacing
            if ($about->image_path) {
                $oldAbsolute = public_path($about->image_path);
                if (File::exists($oldAbsolute)) {
                    File::delete($oldAbsolute);
                }
            }

            $ext      = $file->getClientOriginalExtension();
            $filename = 'about_' . time() . '_' . uniqid() . '.' . $ext;
            $file->move($dir, $filename);

            $about->image_path = 'uploads/about/' . $filename;
        }

        $about->save();

        return redirect()->route('admin.about.edit')
            ->with('status', 'About Us content updated successfully.');
    }
}
