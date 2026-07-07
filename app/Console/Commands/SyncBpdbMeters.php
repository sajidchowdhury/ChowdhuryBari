<?php

namespace App\Console\Commands;

use App\Models\Meter;
use App\Services\BpdbTokenCheckService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Sync meter recharge data from BPDB's official token-check portal.
 *
 * Usage:
 *   php artisan meters:sync-bpdb              # sync all active meters
 *   php artisan meters:sync-bpdb --meter=123  # sync a specific meter by ID
 *   php artisan meters:sync-bpdb --limit=50   # limit to first 50 meters
 *
 * The command fetches the last 3 recharge tokens for each meter from
 * https://web.bpdbprepaid.gov.bd/bn/token-check and:
 *   1. Updates the meter's denormalized last_recharge_amount + last_recharge_at
 *   2. Creates/updates MeterReading records for each of the 3 tokens
 *
 * If BPDB is unreachable (geo-block / downtime), the command logs a warning
 * and exits gracefully — existing data is preserved.
 */
class SyncBpdbMeters extends Command
{
    protected $signature = 'meters:sync-bpdb
                            {--meter= : Sync a specific meter by ID}
                            {--limit= : Maximum number of meters to sync}';

    protected $description = 'Sync meter recharge data from BPDB token-check portal';

    public function handle(BpdbTokenCheckService $bpdb): int
    {
        $this->info('╔══════════════════════════════════════════════════════════════╗');
        $this->info('║   BPDB Meter Sync                                            ║');
        $this->info('╚══════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Build the query
        $query = Meter::where('is_active', true)->where('provider', 'bpdb');

        if ($this->option('meter')) {
            $query->where('id', $this->option('meter'));
        }
        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $meters = $query->get();

        if ($meters->isEmpty()) {
            $this->warn('No active BPDB meters found to sync.');
            return self::SUCCESS;
        }

        $this->info("Found {$meters->count()} meter(s) to sync.");
        $this->newLine();

        $successCount = 0;
        $failCount = 0;

        foreach ($meters as $meter) {
            $this->line("  → Meter #{$meter->id}: {$meter->meter_number} ... ", '');

            $result = $bpdb->getLastTokens($meter->meter_number);

            if ($result === null) {
                $this->line('FAILED (could not reach BPDB or meter not found)', '');
                $this->line('');
                Log::warning("BPDB sync failed for meter {$meter->meter_number}");
                $failCount++;
                continue;
            }

            // Update the meter's denormalized fields
            $meter->update([
                'last_recharge_amount' => $result['last_recharge_amount'],
                'last_recharge_at'     => $result['last_recharge_at'],
                'last_checked_at'      => now(),
            ]);

            // Create/update readings for each token
            foreach ($result['tokens'] as $token) {
                if (!$token['recharged_at']) {
                    continue;
                }

                $readingDate = $token['recharged_at']->copy()->startOfMonth();

                $meter->readings()->updateOrCreate(
                    ['meter_id' => $meter->id, 'reading_date' => $readingDate->toDateString()],
                    [
                        'recharge_amount' => $token['amount'],
                        'recharged_at'    => $token['recharged_at'],
                        'source'          => 'bpdb_api',
                        'notes'           => "Token: {$token['token_number']}" . ($token['status'] ? " ({$token['status']})" : ''),
                    ]
                );
            }

            $lastAmount = $result['last_recharge_amount'] ? '৳' . $result['last_recharge_amount'] : 'no recharge';
            $lastDate = $result['last_recharge_at'] ? $result['last_recharge_at']->format('M d, Y') : 'unknown';
            $this->line("OK — last recharge: {$lastAmount} on {$lastDate}");
            $successCount++;
        }

        $this->newLine();
        $this->info("╔══════════════════════════════════════════════════════════════╗");
        $this->info("║   Sync complete                                              ║");
        $this->info("╠══════════════════════════════════════════════════════════════╣");
        $this->info("║   ✓ {$successCount} meter(s) synced successfully                        ║");
        if ($failCount > 0) {
            $this->info("║   ✗ {$failCount} meter(s) failed                                          ║");
        }
        $this->info("╚══════════════════════════════════════════════════════════════╝");

        return self::SUCCESS;
    }
}
