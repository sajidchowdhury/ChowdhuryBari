<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $fillable = [
        'address',
        'phone',
        'email',
        'whatsapp',
        'office_hours',
        'recipient_email',
        'form_active',
    ];

    protected function casts(): array
    {
        return [
            'form_active' => 'boolean',
        ];
    }

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
                    'address'        => 'চৌধুরীপাড়া',
                    'phone'          => '০১৭১১-২২৩৩৪৪',
                    'email'          => 'info@chowdhuripara.org',
                    'whatsapp'       => '8801711223344',
                    'office_hours'   => 'সকাল ৮টা — রাত ১০টা',
                    'recipient_email' => null,
                    'form_active'    => true,
                ]);
            }
        }
        return $instance;
    }

    /**
     * The WhatsApp link as a wa.me URL.
     */
    public function getWhatsappUrlAttribute(): ?string
    {
        if (!$this->whatsapp) return null;
        $val = trim($this->whatsapp);
        if (str_starts_with($val, 'http')) return $val;
        // strip non-digits
        $digits = preg_replace('/\D/', '', $val);
        return $digits ? 'https://wa.me/' . $digits : null;
    }

    /**
     * Where contact form submissions should be mailed.
     * Falls back to the public email, then to the app default.
     */
    public function getRecipientAttribute(): string
    {
        return $this->recipient_email
            ?: $this->email
            ?: config('mail.from.address');
    }
}
