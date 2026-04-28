<?php

declare(strict_types=1);

use App\Enums\TransferListingStatus;

it('exposes active, sold, and cancelled cases', function (): void {
    expect(TransferListingStatus::cases())->toHaveCount(3)
        ->and(TransferListingStatus::Active->value)->toBe('active')
        ->and(TransferListingStatus::Sold->value)->toBe('sold')
        ->and(TransferListingStatus::Cancelled->value)->toBe('cancelled');
});
