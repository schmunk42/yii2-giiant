<?php

$appSrcPath = '/app/src';

/**
 * Application configuration for acceptance tests
 */
return yii\helpers\ArrayHelper::merge(
    require($appSrcPath.'/config/main.php'),
    require(__DIR__.'/config.php'),
    [
        'controllerNamespace' => 'app\controllers',
        'language' => 'en',
        'components' => [
            'cache' => [
                'class' => 'yii\caching\ApcCache',
            ],
        ],
        'modules' => [
            'sakila' => [
                'class' => 'yii\sakila\Module',
                'allowedIPs' => '*'
            ]
        ]
    ]
);
