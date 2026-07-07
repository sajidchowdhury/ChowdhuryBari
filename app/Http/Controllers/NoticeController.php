<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    /**
     * List all notices (admin).
     */
    public function index()
    {
        $notices = Notice::orderByDesc('published_at')->get();
        return view('admin.notices.index', compact('notices'));
    }

    /**
     * Store a new notice.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'             => ['required', 'string', 'max:255'],
            'headline'         => ['required', 'string', 'max:500'],
            'description'      => ['required', 'string'],
            'published_at'     => ['nullable', 'date'],
            'active_till_date' => ['nullable', 'date'],
            'is_active'        => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['published_at'] = $validated['published_at'] ?? now();

        Notice::create($validated);

        return redirect()->route('admin.notices.index')
            ->with('status', "Notice '{$validated['headline']}' added.");
    }

    /**
     * Update a notice.
     */
    public function update(Request $request, Notice $notice)
    {
        $validated = $request->validate([
            'type'             => ['required', 'string', 'max:255'],
            'headline'         => ['required', 'string', 'max:500'],
            'description'      => ['required', 'string'],
            'published_at'     => ['nullable', 'date'],
            'active_till_date' => ['nullable', 'date'],
            'is_active'        => ['nullable', 'boolean'],
            'sort_order'       => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $notice->update($validated);

        return redirect()->route('admin.notices.index')
            ->with('status', "Notice '{$notice->headline}' updated.");
    }

    /**
     * Delete a notice.
     */
    public function destroy(Notice $notice)
    {
        $headline = $notice->headline;
        $notice->delete();

        return redirect()->route('admin.notices.index')
            ->with('status', "Notice '{$headline}' deleted.");
    }
}
