<?php

namespace App\Http\Controllers;

use App\Models\MemberUpload;
use App\Services\SocialValueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class MemberUploadController extends Controller
{
    public function __construct(protected SocialValueService $socialValue) {}

    /**
     * Member: list their own uploads for the current month.
     * (Also returns upload count + limit for the UI.)
     */
    public function myUploads(Request $request)
    {
        $user = Auth::user();
        $monthKey = MemberUpload::currentMonthKey();

        $uploads = MemberUpload::where('user_id', $user->id)
            ->where('month_key', $monthKey)
            ->latest()
            ->get();

        return response()->json([
            'uploads' => $uploads->map(fn($u) => [
                'id'         => $u->id,
                'image_url'  => $u->image_url,
                'caption'    => $u->caption,
                'stars'      => $u->star_rating,
                'is_rated'   => $u->is_rated,
                'rated_at'   => $u->rated_at?->format('M d, Y'),
                'created_at' => $u->created_at->format('M d, Y'),
            ]),
            'count'      => $uploads->count(),
            'limit'      => SocialValueService::MAX_UPLOADS_PER_MONTH,
            'remaining'  => SocialValueService::MAX_UPLOADS_PER_MONTH - $uploads->count(),
            'month_key'  => $monthKey,
        ]);
    }

    /**
     * Member: upload a new image (max 4 per month).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $monthKey = MemberUpload::currentMonthKey();

        // Enforce the 4-per-month limit
        $currentCount = MemberUpload::where('user_id', $user->id)
            ->where('month_key', $monthKey)
            ->count();

        if ($currentCount >= SocialValueService::MAX_UPLOADS_PER_MONTH) {
            return back()->with('upload_error', "এই মাসে আপনি ইতিমধ্যে " . SocialValueService::MAX_UPLOADS_PER_MONTH . "টি ছবি আপলোড করেছেন। আগামী মাসে আবার আপলোড করতে পারবেন।");
        }

        $validated = $request->validate([
            'image'   => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'caption' => ['nullable', 'string', 'max:200'],
        ]);

        $file = $request->file('image');

        $dir = public_path('uploads/member');
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0775, true);
        }

        $ext      = $file->getClientOriginalExtension();
        $filename = 'yard_' . $user->id . '_' . time() . '_' . uniqid() . '.' . $ext;
        $file->move($dir, $filename);

        MemberUpload::create([
            'user_id'   => $user->id,
            'image_path' => 'uploads/member/' . $filename,
            'caption'   => $validated['caption'] ?? null,
            'month_key' => $monthKey,
        ]);

        return back()->with('upload_success', 'ছবি আপলোড হয়েছে। অ্যাডমিন রিভিউ করে স্কোর দেবেন।');
    }

    /**
     * Member: delete their own unrated upload (current month only).
     */
    public function destroy(Request $request, MemberUpload $upload)
    {
        $user = Auth::user();

        // Ownership + current month + unrated only
        if ($upload->user_id !== $user->id) {
            return back()->with('upload_error', 'এই ছবি মুছার অনুমতি নেই।');
        }
        if ($upload->month_key !== MemberUpload::currentMonthKey()) {
            return back()->with('upload_error', 'পুরনো মাসের ছবি মুছা যায় না।');
        }
        if ($upload->is_rated) {
            return back()->with('upload_error', 'অ্যাডমিন রেট করা ছবি মুছা যায় না।');
        }

        $absolute = public_path($upload->image_path);
        if ($upload->image_path && File::exists($absolute)) {
            File::delete($absolute);
        }
        $upload->delete();

        return back()->with('upload_success', 'ছবি মুছে ফেলা হয়েছে।');
    }

    // ============ ADMIN: Social Value (anonymous rating) ============

    /**
     * Admin: view all uploads for the current month (ANONYMOUS — no member info).
     * Split into unrated (pending) and rated tabs.
     */
    public function adminIndex(Request $request)
    {
        $monthKey = $request->query('month', MemberUpload::currentMonthKey());

        $unrated = MemberUpload::with('rater')
            ->where('month_key', $monthKey)
            ->unrated()
            ->latest()
            ->get();

        $rated = MemberUpload::with('rater')
            ->where('month_key', $monthKey)
            ->rated()
            ->latest('rated_at')
            ->get();

        // Stats for the admin
        $totalThisMonth = MemberUpload::where('month_key', $monthKey)->count();
        $ratedCount = $rated->count();
        $pendingCount = $unrated->count();
        $avgScore = $rated->isNotEmpty() ? round($rated->avg('star_rating'), 1) : null;

        return view('admin.social-value.index', compact('unrated', 'rated', 'monthKey', 'totalThisMonth', 'ratedCount', 'pendingCount', 'avgScore'));
    }

    /**
     * Admin: rate an image 1-10 stars.
     */
    public function rate(Request $request, MemberUpload $upload)
    {
        $validated = $request->validate([
            'star_rating' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $upload->update([
            'star_rating' => $validated['star_rating'],
            'rated_at'    => now(),
            'rated_by'    => Auth::id(),
        ]);

        return redirect()->route('admin.social-value.index')
            ->with('status', "ছবিটিকে {$validated['star_rating']} স্টার দেওয়া হয়েছে।");
    }
}
