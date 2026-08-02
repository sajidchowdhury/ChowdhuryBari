<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure the public/storage symlink exists so that files stored on the
        // 'public' disk (storage/app/public/) are web-accessible at /storage/.
        //
        // This is normally created by `php artisan storage:link`, but in many
        // deployment scenarios (fresh clone, container restart, etc.) the
        // symlink is missing, which causes ALL building/road/field-data images
        // uploaded via the admin panel to return 404 on the public site.
        //
        // We create it idempotently here so the app works out-of-the-box.
        $link = public_path('storage');
        $target = storage_path('app/public');

        if (!File::exists($link) && File::exists($target)) {
            try {
                File::link($target, $link);
            } catch (\Throwable $e) {
                // Silently ignore — symlink creation can fail on restricted
                // environments. The deploy script should handle it separately.
            }
        }
    }
}
