<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\DecimalHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('formatNumber', function ($expression) {
            return "<?php echo \App\Helpers\DecimalHelper::formatEuropean($expression, 2); ?>";
        });

        Blade::directive('formatPrice', function ($expression) {
            return "<?php echo \App\Helpers\DecimalHelper::formatEuropean($expression, 2); ?>";
        });

        Blade::directive('formatQuantity', function ($expression) {
            return "<?php echo \App\Helpers\DecimalHelper::formatEuropean($expression, 2); ?>";
        });

        Blade::directive('money', function ($expression) {
            return "<?php echo \App\Helpers\DecimalHelper::formatEuropean($expression, 2); ?>";
        });
    }
}
