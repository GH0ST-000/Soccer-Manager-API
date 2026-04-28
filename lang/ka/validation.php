<?php

declare(strict_types=1);

return [

    'confirmed' => 'გთოვთ მიუთითოთ :attribute დადასტურების ველი',
    'email' => ':attribute ველი უნდა იყოს ვალიდური ელფოსტის მისამართი.',
    'integer' => ':attribute ველი უნდა იყოს მთელი რიცხვი.',
    'max' => [
        'numeric' => ':attribute ველი არ უნდა იყოს :max-ზე მეტი.',
        'string' => ':attribute ველი არ უნდა შეიცავდეს :max-ზე მეტ სიმბოლოს.',
    ],
    'min' => [
        'numeric' => ':attribute ველი უნდა იყოს მინიმუმ :min.',
        'string' => ':attribute ველი უნდა შეიცავდეს მინიმუმ :min სიმბოლოს.',
    ],
    'required' => ':attribute ველი სავალდებულოა.',
    'string' => ':attribute ველი უნდა იყოს ტექსტი.',
    'unique' => 'მითითებული ელ-ფოსტა უკვე გამოყენებულია',

    'attributes' => [
        'name' => 'სახელის',
        'email' => 'ელფოსტის',
        'password' => 'პაროლის',
        'password_confirmation' => 'პაროლი',
        'country' => 'ქვეყანა',
        'first_name' => 'სახელი',
        'last_name' => 'გვარი',
        'asking_price' => 'ფასი',
        'team_name' => 'გუნდის სახელი',
        'player_name' => 'მოთამაშის სახელი',
        'min_price' => 'მინიმალური ფასი',
        'max_price' => 'მაქსიმალური ფასი',
        'per_page' => 'გვერდზე ჩანაწერები',
    ],

];
