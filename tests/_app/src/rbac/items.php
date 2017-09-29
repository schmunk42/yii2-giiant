<?php
return [
    'sakila' => [
        'type' => 2,
        'description' => '/sakila/* route permission',
    ],
    'Editor' => [
        'type' => 1,
        'description' => 'Editor user',
        'children' => [
            'sakila',
        ],
    ],
];
