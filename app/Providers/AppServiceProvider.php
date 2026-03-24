<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use NumberFormatter;

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
        Blade::directive('money', function ($expression) {
            return "<?php 
                \$formatter = new NumberFormatter('de_DE', NumberFormatter::DECIMAL);
                echo \$formatter->format($expression);
            ?>";
        });
    }
}
