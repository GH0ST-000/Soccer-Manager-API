<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TransferController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->as('auth.')
    ->group(function (): void {
        Route::post('register', 'register')->name('register');
        Route::post('login', 'login')->name('login');
    });
Route::middleware('auth:api')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

    Route::controller(TeamController::class)
        ->prefix('team')
        ->as('team.')
        ->group(function (): void {
            Route::get('/', 'show')->name('show');
            Route::put('/', 'update')->name('update');
        });

    Route::controller(PlayerController::class)
        ->prefix('players')
        ->as('players.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::put('{playerId}', 'update')
                ->whereNumber('playerId')
                ->name('update');
        });

    Route::controller(TransferController::class)
        ->as('transfer.')
        ->group(function (): void {
            Route::get('transfer-list', 'index')->name('index');

            Route::prefix('players/{playerId}/transfer-list')
                ->whereNumber('playerId')
                ->group(function (): void {
                    Route::post('/', 'listPlayer')->name('list');
                    Route::delete('/', 'cancelListing')->name('cancel');
                });

            Route::post('transfer-list/{listingId}/buy', 'buy')
                ->whereNumber('listingId')
                ->name('buy');
        });
});
