<?php

use yii\gii\Module;

$testVendorPath = '/repo/schmunk42/yii2-giiant/tests/_app/vendor';

require($testVendorPath.'/autoload.php');

switch (getenv('GIIANT_TEST_DB')) {
    case 'sakila':
        $giiantTestModule = [
            'sakila' => [
                'class' => 'app\modules\sakila\Module',
                'layout' => '@admin-views/layouts/main',
            ],
            'backend2' => [
                'class' => 'app\modules\backend\Module',
                'layout' => '@admin-views/layouts/main',
            ],
        ];
        break;
    case 'employees':
        $giiantTestModule = [
            'employees' => [
                'class' => 'app\modules\employees\Module',
                'layout' => '@admin-views/layouts/main',
            ],
        ];
        break;

    default:
        $giiantTestModule = [
            getenv('GIIANT_TEST_DB') => [
                'class' => 'app\modules\\'.getenv('GIIANT_TEST_DB').'\Module',
                'layout' => '@admin-views/layouts/main',
            ],
        ];

}

// TODO: add note to dependencies for CRUDs to docs
$giiantTestModule['gridview'] = ['class' => 'kartik\grid\Module'];

if (php_sapi_name() != 'cli') {
    $modules = $giiantTestModule;
}

$modules['gii'] = [
    'class' => Module::class,
    'allowedIPs' => ['*'],
];

return [
    'vendorPath' => $testVendorPath,
    'aliases' => [
        '@tests' => '@vendor/schmunk42/yii2-giiant/tests',
        '@common' => '@app/common',
        '@backend' => '@app/modules/backend',
    ],
    'bootstrap' => [
        'gii',
    ],
    'components' => [
        /*'cache' => [
            'class' => 'yii\caching\ApcCache',
        ],*/
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.getenv('DB_PORT_3306_TCP_ADDR').';dbname='.getenv('GIIANT_TEST_DB'),
            // DATABASE_DSN_DB
            'username' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'charset' => 'utf8',
            'tablePrefix' => getenv('DATABASE_TABLE_PREFIX'),
            'enableSchemaCache' => true,
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                ],
            ],
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\User',
        ],
    ],
    'modules' => $modules,
    'params' => [
        'yii.migrations' => [
            '@app/src/migrations/test',
        ],
    ],
];