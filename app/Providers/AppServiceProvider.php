<?php

namespace App\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        URL::forceScheme('https');

        if($cookie = Cookie::get('sipay_api_config')){

            $c = Crypt::decrypt($cookie, false);
            $b = explode("|",$c);

            $cookie_data = stripslashes($b[1]);
            $api_config = json_decode($cookie_data, true);

            Config::set('payment.sipay.api_url', $api_config["api_url"]);
            Config::set('payment.sipay.merchant_id', $api_config["merchant_id"]);
            Config::set('payment.sipay.api_merchant_key', $api_config["merchant_key"]);
            Config::set('payment.sipay.app_key', $api_config["app_key"]);
            Config::set('payment.sipay.app_secret', $api_config["app_secret"]);
        }

        view()->share('sipay_api_url', Config::get('payment.sipay.api_url'));
    }
}
