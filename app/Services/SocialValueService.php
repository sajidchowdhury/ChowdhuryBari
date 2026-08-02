<?php

namespace App\Services;

use App\Models\MemberUpload;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Social Value + Ranking logic for the member cleanliness program.
 *
 * SCORING FORMULA (shown to members + admin):
 *   social_value = round( avg(star_rating of rated images this month) × 10 )
 *
 *   - Each image is rated 1-10 by the admin (anonymous).
 *   - A member's monthly social value is the average of their rated images
 *     × 10, giving a 10-100 scale (rounded).
 *   - Unrated images don't count toward the average.
 *   - A member with NO rated images → social value = null (shown as "--").
 *
 * RANKING:
 *   - Members ranked by current month's social value (desc).
 *   - Ties broken by LAST month's social value (desc).
 *   - Members with null social value get no rank ("-- upload image for ranking").
 */
class SocialValueService
{
    /** Max uploads per member per month. */
    const MAX_UPLOADS_PER_MONTH = 4;

    /**
     * Compute social value for a user in a month.
     * Returns int (10-100) or null if no rated images.
     */
    public function socialValue(int $userId, string $monthKey): ?int
    {
        $value = MemberUpload::socialValueFor($userId, $monthKey);
        return $value !== null ? (int) $value : null;
    }

    /**
     * Current month's social value for a user.
     */
    public function currentSocialValue(int $userId): ?int
    {
        return $this->socialValue($userId, MemberUpload::currentMonthKey());
    }

    /**
     * Last month's social value for a user.
     */
    public function previousSocialValue(int $userId): ?int
    {
        return $this->socialValue($userId, MemberUpload::previousMonthKey());
    }

    /**
     * Build the ranked leaderboard for a given month.
     * Returns a Collection of objects:
     *   { user_id, name, phone, building_name, owner_name,
     *     social_value, prev_social_value, rank, best_image_url, best_image_stars,
     *     upload_count, rated_count }
     *
     * Ties in current social value are broken by previous month's value (desc).
     * Members with null social value are excluded from the ranked list
     * (returned separately if requested).
     */
    public function leaderboard(string $monthKey, ?string $prevMonthKey = null, int $limit = null): Collection
    {
        $prevMonthKey ??= MemberUpload::previousMonthKey();

        // Gather all members (non-admin, active) who uploaded this month.
        $users = User::where('role', '!=', 'admin')
            ->where('is_active', true)
            ->whereHas('uploads', fn($q) => $q->where('month_key', $monthKey))
            ->get();

        $rows = $users->map(function ($user) use ($monthKey, $prevMonthKey) {
            $current = $this->socialValue($user->id, $monthKey);
            $prev    = $this->socialValue($user->id, $prevMonthKey);

            $uploads = MemberUpload::where('user_id', $user->id)->where('month_key', $monthKey)->get();
            $rated   = $uploads->where('star_rating', '!==', null);
            $best    = MemberUpload::bestImageFor($user->id, $monthKey);
            $building = $user->building;

            return (object) [
                'user_id'           => $user->id,
                'name'              => $user->name,
                'phone'             => $user->phone,
                'building_name'     => $building?->name ?? '—',
                'owner_name'        => $building?->owner_name ?? $user->name,
                'road_name'         => $building?->road?->name ?? '—',
                'social_value'      => $current,
                'prev_social_value' => $prev,
                'best_image_url'    => $best?->image_url,
                'best_image_stars'  => $best?->star_rating,
                'upload_count'      => $uploads->count(),
                'rated_count'       => $rated->count(),
            ];
        });

        // Only rank members who have a current social value (>=1 rated image).
        $ranked = $rows->filter(fn($r) => $r->social_value !== null)
            ->sortByDesc('social_value')
            ->sortByDesc('prev_social_value')   // tie-breaker (stable sort keeps primary order)
            ->values();

        // Assign rank numbers
        $ranked->each(fn($r, $i) => $r->rank = $i + 1);

        return $limit ? $ranked->take($limit) : $ranked;
    }

    /**
     * A single member's rank for the current month.
     * Returns int or null (if they have no social value).
     */
    public function rankFor(int $userId): ?int
    {
        $board = $this->leaderboard(MemberUpload::currentMonthKey());
        $row = $board->firstWhere('user_id', $userId);
        return $row?->rank;
    }

    /**
     * Total number of ranked members this month (for "X জনের মধ্যে" text).
     */
    public function totalRankedMembers(): int
    {
        return User::where('role', '!=', 'admin')
            ->where('is_active', true)
            ->whereHas('uploads', fn($q) => $q->where('month_key', MemberUpload::currentMonthKey())->rated())
            ->count();
    }
}
