<?php

declare(strict_types=1);

namespace App\Shared\Salla;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Salla\Endpoints\BrandsEndpoint;
use App\Shared\Salla\Endpoints\CategoriesEndpoint;
use App\Shared\Salla\Endpoints\CustomersEndpoint;
use App\Shared\Salla\Endpoints\OrdersEndpoint;
use App\Shared\Salla\Endpoints\ProductsEndpoint;
use App\Shared\Salla\Endpoints\WebhooksEndpoint;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

final class SallaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SallaAuthenticator::class, static function (Application $app): SallaAuthenticator {
            return new SallaAuthenticator(
                clientId:     (string) config("salla.client_id"),
                clientSecret: (string) config("salla.client_secret"),
                redirectUri:  (string) config("salla.redirect_uri"),
            );
        });

        $this->app->singleton(SallaClientContract::class, static function (Application $app): SallaClientContract {
            return new SallaClient(
                $app->make(SallaAuthenticator::class),
            );
        });

        $this->app->singleton(ProductsEndpoint::class,    static fn (Application $a) => new ProductsEndpoint($a->make(SallaClientContract::class)));
        $this->app->singleton(OrdersEndpoint::class,      static fn (Application $a) => new OrdersEndpoint($a->make(SallaClientContract::class)));
        $this->app->singleton(CustomersEndpoint::class,   static fn (Application $a) => new CustomersEndpoint($a->make(SallaClientContract::class)));
        $this->app->singleton(CategoriesEndpoint::class,  static fn (Application $a) => new CategoriesEndpoint($a->make(SallaClientContract::class)));
        $this->app->singleton(BrandsEndpoint::class,      static fn (Application $a) => new BrandsEndpoint($a->make(SallaClientContract::class)));
        $this->app->singleton(WebhooksEndpoint::class,    static fn (Application $a) => new WebhooksEndpoint($a->make(SallaClientContract::class)));
    }

    public function boot(): void
    {
        // No boot logic required at this stage.
    }
}
