<?php

declare(strict_types=1);

namespace App\Enums;

enum TransferListingStatus: string
{
    case Active = 'active';
    case Sold = 'sold';
    case Cancelled = 'cancelled';
}
