<?php

declare(strict_types=1);

return [

    'confirmed' => 'The :attribute field confirmation does not match.',
    'email' => 'The :attribute field must be a valid email address.',
    'integer' => 'The :attribute field must be an integer.',
    'max' => [
        'numeric' => 'The :attribute field must not be greater than :max.',
        'string' => 'The :attribute field must not be greater than :max characters.',
    ],
    'min' => [
        'numeric' => 'The :attribute field must be at least :min.',
        'string' => 'The :attribute field must be at least :min characters.',
    ],
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute field must be a string.',
    'unique' => 'The :attribute has already been taken.',

    'attributes' => [
        'first_name' => 'first name',
        'last_name' => 'last name',
        'asking_price' => 'asking price',
        'team_name' => 'team name',
        'player_name' => 'player name',
        'min_price' => 'minimum price',
        'max_price' => 'maximum price',
        'per_page' => 'per page',
        'password_confirmation' => 'password confirmation',
    ],

];
