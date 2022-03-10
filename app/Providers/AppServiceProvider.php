<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Websitesetting;
use Illuminate\Support\Facades\Blade;

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
        Schema::defaultStringLength(191);
        $appSetting = Websitesetting::first();
        \View::share('appSetting', $appSetting);
        
        Blade::directive('convert2', function ($money) {
        return "<?php echo number_format($money, 0,',','.'); ?>";
        });
        
    }
}
