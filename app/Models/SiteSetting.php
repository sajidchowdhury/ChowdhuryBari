<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'logo_path',
        'nav_color',
        'whatsapp_link',
        'facebook_link',
        'youtube_link',
        'footer_address',
    ];

    /**
     * Memoized singleton — fetches (and creates if missing) the one row.
     * Cached for the duration of a single request.
     */
    public static function cached(): self
    {
        static $instance = null;
        if ($instance === null) {
            $instance = self::first();
            if (!$instance) {
                $instance = self::create([
                    'logo_path'      => null,
                    'nav_color'      => null,
                    'whatsapp_link'  => null,
                    'facebook_link'  => null,
                    'youtube_link'   => null,
                    'footer_address' => 'চৌধুরীপাড়া',
                ]);
            }
        }
        return $instance;
    }

    /**
     * Public web URL for the logo (falls back to default asset).
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            return asset($this->logo_path);
        }
        return asset('img/logo.png');
    }

    /**
     * Whether a custom navbar color has been set.
     */
    public function hasNavColor(): bool
    {
        return !empty($this->nav_color);
    }
}
