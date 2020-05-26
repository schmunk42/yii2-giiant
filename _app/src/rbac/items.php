<?php
return [
    'SakilaActorFull' => [
        'type' => 2,
        'description' => 'sakila CRUD permission',
    ],
    'SakilaCategoryFull' => [
        'type' => 2,
        'description' => 'sakila CRUD permission',
    ],
    'sakila' => [
        'type' => 2,
        'description' => '/sakila/* route permission',
    ],
    'Editor' => [
        'type' => 1,
        'description' => 'Editor user',
        'children' => [
            'sakila',
            'SakilaActorFull',
            'SakilaCategoryFull',
        ],
    ],
];
