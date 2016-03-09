<?php

$appSrcPath = '/app/src';

/**
 * Application configuration for acceptance tests
 */
return yii\helpers\ArrayHelper::merge(
    require($appSrcPath . '/config/main.php'),
    require(__DIR__ . '/config.php'),
    [
        'controllerNamespace' => 'app\controllers',
        'language'            => 'en',
    ]
);
