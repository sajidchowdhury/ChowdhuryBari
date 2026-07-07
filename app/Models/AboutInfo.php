<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AboutInfo extends Model
{
    protected $fillable = [
        'headline',
        'description',
        'image_path',
    ];

    /**
     * Get the single About row (creates a default if missing).
     */
    public static function current(): self
    {
        $info = self::first();

        if (!$info) {
            $info = self::create([
                'headline'    => 'আমরা কারা?',
                'description' => 'চৌধুরীপাড়াস্থ সমাজ উন্নায়ন সংস্থা একটি সম্পূর্ণ স্বেচ্ছাসেবী, সমাজ-চালিত সংগঠন।',
                'image_path'  => null,
            ]);
        }

        return $info;
    }

    /**
     * Public web URL for the stored image (falls back to a default asset).
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset($this->image_path);
        }
        return asset('img/aboutus.jpg');
    }
}
