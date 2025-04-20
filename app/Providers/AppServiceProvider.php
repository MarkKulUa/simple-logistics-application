<?php

namespace App\Providers;

//use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
//use App\Services\Communication\TwilioService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->singleton(TwilioService::class, function ($app) {
//            return new TwilioService(
//                Config::get('services.twilio.account_sid'),
//                Config::get('services.twilio.auth_token'),
//                Config::get('services.twilio.from_number')
//            );
//        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}
