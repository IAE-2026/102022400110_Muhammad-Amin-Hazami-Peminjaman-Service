<?php

namespace App\Providers;

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
        // 1. Fallback dinamis ke SQLite jika host MySQL tidak terjangkau / gagal resolve
        $dbConnection = config('database.default');
        if ($dbConnection === 'mysql') {
            $dbHost = config('database.connections.mysql.host');
            if ($dbHost && $dbHost !== '127.0.0.1' && $dbHost !== 'localhost') {
                if (gethostbyname($dbHost) === $dbHost) {
                    config(['database.default' => 'sqlite']);
                }
            }
        }

        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
        } catch (\Throwable $e) {
            if (config('database.default') === 'mysql') {
                config(['database.default' => 'sqlite']);
            }
        }

        // 2. Auto-migrate dan Auto-seed jika tabel 'loans' belum terbentuk di database aktif
        try {
            if (! \Illuminate\Support\Facades\Schema::hasTable('loans')) {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            // Abaikan kesalahan koneksi saat bootstrap
        }
    }
}
