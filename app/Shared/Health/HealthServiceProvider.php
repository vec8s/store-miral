<?php

declare(strict_types=1);

namespace App\Shared\Health;

use App\Shared\Contracts\SallaClientContract;
use App\Shared\Health\Checks\CacheHealthCheck;
use App\Shared\Health\Checks\DatabaseHealthCheck;
use App\Shared\Health\Checks\MailHealthCheck;
use App\Shared\Health\Checks\QueueHealthCheck;
use App\Shared\Health\Checks\SallaHealthCheck;
use App\Shared\Health\Checks\StorageHealthCheck;
use App\Shared\Health\Http\HealthController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\ServiceProvider;

final class HealthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(HealthController::class, static function (Application $app): HealthController {
            return new HealthController([
                $app->make(DatabaseHealthCheck::class),
                $app->make(CacheHealthCheck::class),
                $app->make(QueueHealthCheck::class),
                $app->make(StorageHealthCheck::class),
                $app->make(MailHealthCheck::class),
                $app->make(SallaHealthCheck::class),
            ]);
        });

        $this->app->bind(DatabaseHealthCheck::class);
        $this->app->bind(CacheHealthCheck::class);
        $this->app->bind(QueueHealthCheck::class);
        $this->app->bind(StorageHealthCheck::class);
        $this->app->bind(MailHealthCheck::class);

        $this->app->singleton(SallaHealthCheck::class, static fn (Application $app): SallaHealthCheck => new SallaHealthCheck(
            $app->make(SallaClientContract::class),
        ));
    }

    public function boot(Router $router): void
    {
        $router->get('/up', HealthController::class);
    }
}