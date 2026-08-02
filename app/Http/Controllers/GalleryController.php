<?php

namespace App\Http\Controllers;

use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    /**
     * List all gallery items (admin).
     */
    public function index()
    {
        $items = GalleryItem::latestFirst()->get();
        return view('admin.gallery.index', compact('items'));
    }

    /**
     * Store a new gallery image + short description.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'image'      => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'], // 5MB
            'caption'    => ['required', 'string', 'max:255'],
            'category'   => ['nullable', 'string', 'max:100'],
            'is_active'  => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $file = $request->file('image');

        // Store directly in public/uploads/gallery/ — no storage:link needed.
        $dir = public_path('uploads/gallery');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0775, true);
        }

        $ext       = $file->getClientOriginalExtension();
        $filename  = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move($dir, $filename);

        GalleryItem::create([
            'image_path' => 'uploads/gallery/' . $filename,
            'caption'    => $validated['caption'],
            'category'   => $validated['category'] ?? null,
            'is_active'  => $request->boolean('is_active', true),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('admin.gallery.index')
            ->with('status', "Image '{$validated['caption']}' uploaded.");
    }

    /**
     * Delete a gallery item and its file.
     */
    public function destroy(GalleryItem $gallery)
    {
        $caption = $gallery->caption;

        // Remove the physical file
        $absolute = public_path($gallery->image_path);
        if ($gallery->image_path && File::exists($absolute)) {
            File::delete($absolute);
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('status', "Image '{$caption}' deleted.");
    }
}
