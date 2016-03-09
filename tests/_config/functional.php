<?php
/**
 * Application configuration for unit tests
 */

$_SERVER['SCRIPT_FILENAME'] = YII_TEST_ENTRY_FILE;
$_SERVER['SCRIPT_NAME']     = YII_TEST_ENTRY_URL;

$basePath = '/app';

return yii\helpers\ArrayHelper::merge(
    require($basePath . '/src/config/main.php'),
    require(__DIR__ . '/config.php'),
    [
        'controllerNamespace' => 'app\controllers',
        'components'          => [
            'request' => [
                // it's not recommended to run functional tests with CSRF validation enabled
                'enableCsrfValidation' => false,
                'cookieValidationKey'  => uniqid('TESTING-'),
                // but if you absolutely need it set cookie domain to localhost
                /*
                'csrfCookie' => [
                    'domain' => 'localhost',
                ],
                */
            ],
        ],
    ]
);
