<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    /**
     * List all members (admin).
     */
    public function index()
    {
        $members = Member::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.members.index', compact('members'));
    }

    /**
     * Store a new member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'designation'  => ['required', 'string', 'max:255'],
            'started_from' => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:255'],
            'bio'          => ['nullable', 'string'],
            'image'        => ['nullable', 'file', 'image', 'max:5120'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('members', 'public');
        }

        unset($validated['image']);
        Member::create($validated);

        return redirect()->route('admin.members.index')
            ->with('status', "Member '{$validated['name']}' added.");
    }

    /**
     * Update a member.
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'designation'  => ['required', 'string', 'max:255'],
            'started_from' => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:255'],
            'bio'          => ['nullable', 'string'],
            'image'        => ['nullable', 'file', 'image', 'max:5120'],
            'sort_order'   => ['nullable', 'integer', 'min:0'],
            'is_active'    => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($member->image_path) {
                Storage::disk('public')->delete($member->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('members', 'public');
        }

        unset($validated['image']);
        $member->update($validated);

        return redirect()->route('admin.members.index')
            ->with('status', "Member '{$member->name}' updated.");
    }

    /**
     * Delete a member.
     */
    public function destroy(Member $member)
    {
        $name = $member->name;
        if ($member->image_path) {
            Storage::disk('public')->delete($member->image_path);
        }
        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('status', "Member '{$name}' deleted.");
    }
}
