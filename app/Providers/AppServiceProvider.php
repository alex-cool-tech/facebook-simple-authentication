<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\FacebookApiUrlProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->app->bind(FacebookApiUrlProvider::class, function () {
            return new FacebookApiUrlProvider(
                config('facebook_api.login_url'),
                config('facebook_api.graph_url'),
                config('facebook_api.client_id'),
                config('facebook_api.client_secret')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
