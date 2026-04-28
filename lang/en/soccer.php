<?php

declare(strict_types=1);

return [
    'auth' => [
        'registered' => 'Registration successful.',
        'logged_in' => 'Logged in successfully.',
        'logged_out' => 'Logged out successfully.',
        'invalid_credentials' => 'Invalid email or password.',
        'unauthenticated' => 'Authentication required.',
    ],
    'team' => [
        'updated' => 'Team updated successfully.',
        'not_found' => 'Team not found.',
    ],
    'player' => [
        'updated' => 'Player updated successfully.',
        'not_found' => 'Player not found.',
    ],
    'transfer' => [
        'listed' => 'Player listed on the transfer market.',
        'cancelled' => 'Transfer listing cancelled.',
        'bought' => 'Player purchased successfully.',
        'already_listed' => 'This player is already on the transfer list.',
        'not_listed' => 'This player is not currently on the transfer list.',
        'listing_not_active' => 'This transfer listing is no longer active.',
        'cannot_buy_own_player' => 'You cannot buy a player from your own team.',
        'insufficient_budget' => 'Your team does not have enough budget for this transfer.',
    ],
];
