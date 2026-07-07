<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * BPDB Prepaid Meter Token Check Service
 *
 * ⚠️  IMPORTANT — reCAPTCHA BLOCKER (confirmed July 2026):
 *   The official BPDB portal at https://web.bpdbprepaid.gov.bd/bn/token-check
 *   uses Google reCAPTCHA Enterprise. There is NO legitimate way to bypass
 *   this programmatically:
 *     - Paid CAPTCHA-solving services (2captcha etc.) are a legal gray area
 *       and Google actively fights them
 *     - Headless browsers are caught by reCAPTCHA Enterprise's behavioral
 *       analysis (mouse movement, timing, fingerprinting)
 *     - The only "right" way is to become an official BPDB API partner
 *       (bureaucratic, takes months)
 *
 * WHAT WORKS INSTEAD — human-in-the-loop:
 *   The secretary (human) visits the BPDB site, solves the CAPTCHA, enters
 *   the meter number, and sees the last 3 recharge tokens. They then enter
 *   those tokens into our admin panel via the "Quick Recharge Entry" modal.
 *
 *   We make this as painless as possible by:
 *     1. Providing an "Open BPDB Site ↗" button that opens the portal in a
 *        new tab (so the secretary doesn't lose their place in our admin)
 *     2. A "Quick Recharge Entry" modal pre-filled with the meter number
 *        where the secretary pastes the 3 token amounts + dates
 *     3. One submit creates all 3 MeterReading records at once
 *
 * This service is KEPT for future use IF BPDB ever offers an official API
 * (or if the owner becomes an authorized partner). Until then, the
 * getLastTokens() method will likely return null because of reCAPTCHA.
 *
 * Official URL: https://web.bpdbprepaid.gov.bd/bn/token-check
 */
class BpdbTokenCheckService
{
    private const PRIMARY_URL = 'https://web.bpdbprepaid.gov.bd';
    private const FALLBACK_URL = 'http://180.211.137.10:3001';

    private const TIMEOUT = 30;

    /**
     * Get the URL to the BPDB token-check page (for humans to visit).
     * Used by the "Open BPDB Site ↗" button in the admin UI.
     */
    public function getTokenCheckUrl(): string
    {
        return self::PRIMARY_URL . '/bn/token-check';
    }

    /**
     * Fetch the last 3 recharge tokens for a meter number.
     *
     * ⚠️ ALMOST CERTAIN TO RETURN NULL because of reCAPTCHA Enterprise.
     * See class docblock above. Kept for the day BPDB offers a real API.
     */
    public function getLastTokens(string $meterNumber): ?array
    {
        $meterNumber = trim($meterNumber);
        if (empty($meterNumber)) {
            return null;
        }

        // Try the modern HTTPS endpoint first
        $result = $this->tryFetch(self::PRIMARY_URL, $meterNumber);

        // Fall back to the older HTTP IP-based endpoint
        if ($result === null) {
            Log::info("BPDB: primary URL failed for meter {$meterNumber}, trying fallback...");
            $result = $this->tryFetch(self::FALLBACK_URL, $meterNumber);
        }

        if ($result === null) {
            Log::warning("BPDB: could not fetch tokens for meter {$meterNumber} from any URL");
            return null;
        }

        return $result;
    }

    /**
     * Try fetching from a specific BPDB base URL.
     */
    private function tryFetch(string $baseUrl, string $meterNumber): ?array
    {
        try {
            // Step 1: GET the token-check page to establish a session + find the form endpoint
            $sessionResponse = Http::withHeaders([
                'User-Agent' => $this->userAgent(),
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language' => 'bn,en-US;q=0.9,en;q=0.8',
            ])
            ->timeout(self::TIMEOUT)
            ->connectTimeout(15)
            ->get("{$baseUrl}/bn/token-check");

            if (!$sessionResponse->successful()) {
                Log::info("BPDB: GET token-check page failed with status {$sessionResponse->status()} from {$baseUrl}");
                return null;
            }

            $html = $sessionResponse->body();
            $cookies = $sessionResponse->cookies();

            // Step 2: Find the form action / API endpoint from the HTML
            // The page typically has a form like <form action="/api/token-check" method="POST">
            // or an AJAX endpoint referenced in JS.
            $formAction = $this->extractFormAction($html);

            if (!$formAction) {
                // If we can't find the form action, try common known endpoints
                $formAction = '/api/token-check';
            }

            // Make the action a full URL
            $submitUrl = $this->resolveUrl($baseUrl, $formAction);

            // Step 3: Submit the meter number
            // Try as form POST first (most common)
            $submitResponse = Http::withHeaders([
                'User-Agent' => $this->userAgent(),
                'Accept' => 'application/json,text/javascript,*/*;q=0.01',
                'Accept-Language' => 'bn,en-US;q=0.9,en;q=0.8',
                'X-Requested-With' => 'XMLHttpRequest',
                'Referer' => "{$baseUrl}/bn/token-check",
            ])
            ->withCookies($cookies->toArray(), parse_url($baseUrl, PHP_URL_HOST))
            ->timeout(self::TIMEOUT)
            ->connectTimeout(15)
            ->asForm()
            ->post($submitUrl, [
                'meter_number' => $meterNumber,
                'meter_no' => $meterNumber, // try both field names
            ]);

            if (!$submitResponse->successful()) {
                Log::info("BPDB: POST submit failed with status {$submitResponse->status()} from {$submitUrl}");
                return null;
            }

            $body = $submitResponse->body();
            $contentType = $submitResponse->header('Content-Type') ?? '';

            // Step 4: Parse the response
            if (str_contains($contentType, 'application/json')) {
                return $this->parseJsonResponse($submitResponse->json(), $meterNumber);
            }

            // HTML response — scrape the result table
            return $this->parseHtmlResponse($body, $meterNumber);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::info("BPDB: connection failed to {$baseUrl}: " . $e->getMessage());
            return null;
        } catch (\Exception $e) {
            Log::warning("BPDB: unexpected error fetching from {$baseUrl}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Extract the form action URL from the token-check page HTML.
     */
    private function extractFormAction(string $html): ?string
    {
        // Look for <form action="...">
        if (preg_match('/<form[^>]+action=["\']([^"\']+)["\']/i', $html, $matches)) {
            return $matches[1];
        }

        // Look for fetch() / $.ajax URL in JS
        if (preg_match('/(?:fetch|ajax|url)\s*[:=(\s]+["\']([^"\']*token[^"\']*)["\']/i', $html, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Resolve a possibly-relative URL against a base URL.
     */
    private function resolveUrl(string $baseUrl, string $action): string
    {
        if (str_starts_with($action, 'http://') || str_starts_with($action, 'https://')) {
            return $action;
        }
        if (str_starts_with($action, '//')) {
            return parse_url($baseUrl, PHP_URL_SCHEME) . ':' . $action;
        }
        if (str_starts_with($action, '/')) {
            return rtrim($baseUrl, '/') . $action;
        }
        return rtrim($baseUrl, '/') . '/' . $action;
    }

    /**
     * Parse a JSON API response.
     */
    private function parseJsonResponse(?array $json, string $meterNumber): ?array
    {
        if (empty($json)) {
            return null;
        }

        // The exact JSON structure is unknown without testing against the live site.
        // We handle common shapes and log the raw response for debugging.
        Log::info("BPDB: JSON response for meter {$meterNumber}: " . json_encode($json));

        $customer = $json['data'] ?? $json['customer'] ?? $json['result'] ?? $json;
        $tokens = $customer['tokens'] ?? $customer['recharges'] ?? $customer['last_tokens'] ?? [];

        $parsedTokens = [];
        foreach ($tokens as $token) {
            $parsedTokens[] = [
                'token_number'  => $token['token'] ?? $token['token_number'] ?? null,
                'amount'        => (float) ($token['amount'] ?? $token['recharge_amount'] ?? $token['value'] ?? 0),
                'recharged_at'  => isset($token['date']) ? Carbon::parse($token['date']) : (isset($token['recharged_at']) ? Carbon::parse($token['recharged_at']) : null),
                'status'        => $token['status'] ?? null,
            ];
        }

        if (empty($parsedTokens)) {
            return null;
        }

        // Sort by recharged_at desc (most recent first)
        usort($parsedTokens, function ($a, $b) {
            if (!$a['recharged_at']) return 1;
            if (!$b['recharged_at']) return -1;
            return $b['recharged_at']->timestamp - $a['recharged_at']->timestamp;
        });

        return [
            'customer_name'         => $customer['name'] ?? $customer['customer_name'] ?? null,
            'account_no'            => $customer['account_no'] ?? $customer['account_number'] ?? null,
            'meter_no'              => $customer['meter_no'] ?? $customer['meter_number'] ?? $meterNumber,
            'tokens'                => $parsedTokens,
            'last_recharge_amount'  => $parsedTokens[0]['amount'] ?? null,
            'last_recharge_at'      => $parsedTokens[0]['recharged_at'] ?? null,
        ];
    }

    /**
     * Parse an HTML response (scrape the result table).
     */
    private function parseHtmlResponse(string $html, string $meterNumber): ?array
    {
        // The HTML contains a table with customer info + token rows.
        // We use simple regex parsing (no DOM parser to keep deps light).

        $customerName = null;
        $accountNo = null;

        // Try to extract customer name
        if (preg_match('/Customer Name[^<]*<[^>]*>([^<]+)/i', $html, $m)) {
            $customerName = trim($m[1]);
        }
        // Try Bengali label
        if (!$customerName && preg_match('/গ্রাহকের নাম[^<]*<[^>]*>([^<]+)/i', $html, $m)) {
            $customerName = trim($m[1]);
        }

        // Try to extract account number
        if (preg_match('/Account No[^<]*<[^>]*>([^<]+)/i', $html, $m)) {
            $accountNo = trim($m[1]);
        }

        // Extract token rows — typically in a table with columns:
        // Token Number | Amount | Date | Status
        $tokens = [];
        $pattern = '/<tr[^>]*>\s*<td[^>]*>([^<]+)<\/td>\s*<td[^>]*>([^<]+)<\/td>\s*<td[^>]*>([^<]+)<\/td>\s*(?:<td[^>]*>([^<]+)<\/td>\s*)?<\/tr>/i';

        if (preg_match_all($pattern, $html, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $row) {
                $tokenNumber = trim($row[1]);
                // Skip header rows
                if (stripos($tokenNumber, 'token') !== false || stripos($tokenNumber, 'টোকেন') !== false) {
                    continue;
                }
                $amount = (float) preg_replace('/[^0-9.]/', '', $row[2]);
                $dateStr = trim($row[3]);
                $status = isset($row[4]) ? trim($row[4]) : null;

                try {
                    $rechargedAt = Carbon::parse($dateStr);
                } catch (\Exception $e) {
                    $rechargedAt = null;
                }

                $tokens[] = [
                    'token_number'  => $tokenNumber,
                    'amount'        => $amount,
                    'recharged_at'  => $rechargedAt,
                    'status'        => $status,
                ];
            }
        }

        if (empty($tokens)) {
            Log::info("BPDB: no tokens parsed from HTML for meter {$meterNumber}");
            return null;
        }

        return [
            'customer_name'         => $customerName,
            'account_no'            => $accountNo,
            'meter_no'              => $meterNumber,
            'tokens'                => $tokens,
            'last_recharge_amount'  => $tokens[0]['amount'] ?? null,
            'last_recharge_at'      => $tokens[0]['recharged_at'] ?? null,
        ];
    }

    /**
     * A realistic browser User-Agent to avoid bot detection.
     */
    private function userAgent(): string
    {
        return 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36';
    }
}
