<?php

declare(strict_types=1);
use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Shared\Health\HealthServiceProvider;
use App\Shared\Salla\SallaServiceProvider;

return [
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    SallaServiceProvider::class,
    HealthServiceProvider::class,
];
