<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        $this->app->singleton(\Kreait\Firebase\Contract\Messaging::class, function () {
            $credentials = config('services.firebase.credentials_path');
            if (!$credentials) {
                throw new \RuntimeException('FIREBASE_CREDENTIALS env var is not set');
            }
            return (new \Kreait\Firebase\Factory())
                ->withServiceAccount($credentials)
                ->createMessaging();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \App\Models\order_product::observe(\App\Observers\OrderProductObserver::class);
    }
}
