<?php
/**
 * Application configuration shared by all test types
 */
return [
    'bootstrap' => [
        'gii'
    ],
    'modules'   => [
        'gii' => [
            'class'      => 'yii\gii\Module',
            'allowedIPs' => '*'
        ]
    ],
];
