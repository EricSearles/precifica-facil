<?php

namespace App\Providers;

use App\Support\CompanyFormatter;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
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
        Schema::defaultStringLength(191);

        Blade::directive('money', function ($expression) {
            return "<?php echo app('".CompanyFormatter::class."')->money($expression); ?>";
        });
    }
}
