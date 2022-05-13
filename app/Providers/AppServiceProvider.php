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
                env('FACEBOOK_API_LOGIN_URL'),
                env('FACEBOOK_API_GRAPH_URL'),
                env('FACEBOOK_API_CLIENT_ID'),
                env('FACEBOOK_API_CLIENT_SECRET')
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
