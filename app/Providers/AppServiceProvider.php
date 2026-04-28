<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\PlayerRepositoryInterface;
use App\Repositories\Contracts\TeamRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\TransferListingRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\TransferListingRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bind repository interfaces to their implementations.
     *
     * @var array<class-string, class-string>
     */
    private const array REPOSITORY_BINDINGS = [
        UserRepositoryInterface::class => UserRepository::class,
        TeamRepositoryInterface::class => TeamRepository::class,
        PlayerRepositoryInterface::class => PlayerRepository::class,
        TransferListingRepositoryInterface::class => TransferListingRepository::class,
        TransactionRepositoryInterface::class => TransactionRepository::class,
    ];

    public function register(): void
    {
        foreach (self::REPOSITORY_BINDINGS as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
    }

    public function boot(): void
    {
        //
    }
}
